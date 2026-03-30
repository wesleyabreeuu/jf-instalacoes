<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;


class ServicoController extends Controller
{
    private function routeBase(): string
    {
        return auth()->user()?->isAdmin() ? 'admin.servicos' : 'app.servicos';
    }

    private function ensureCanAccess(Servico $servico): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }
    }

    public function index(Request $request)
    {
        $query = Servico::with(['cliente', 'colaborador'])->latest();
        $user = auth()->user();

        // filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // filtro por colaborador
        if ($request->filled('colaborador_id') && $user?->isAdmin()) {
            $query->where('colaborador_id', $request->colaborador_id);
        }

        // filtro por data (de/até)
        if ($request->filled('data_de')) {
            $query->whereDate('data', '>=', $request->data_de);
        }
        if ($request->filled('data_ate')) {
            $query->whereDate('data', '<=', $request->data_ate);
        }

        $servicos = $query->paginate(10)->withQueryString();

        // listas para o filtro
        $colaboradores = User::orderBy('name')->get(['id', 'name']);

        $statusList = [
            'agendado' => 'Agendado',
            'aberto' => 'Aberto',
            'em_execucao' => 'Em execução',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado',
        ];

        $routeBase = $this->routeBase();

        return view('admin.servicos.index', compact('servicos', 'colaboradores', 'statusList', 'routeBase'));
    }

    public function create()
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403);
        }

        $clientes = Cliente::orderBy('nome')->get(['id', 'nome', 'rua', 'numero', 'bairro', 'cidade', 'uf']);
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('admin.servicos.create', compact('clientes', 'usuarios'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'colaborador_id' => ['required', 'exists:users,id'],
            'local_instalacao' => ['nullable', 'string', 'max:255'],
            'data' => ['required', 'date'],
            'hora_prevista' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:agendado,aberto,em_execucao,finalizado,cancelado'],
        ]);

        // Se não veio local_instalacao, preenche com endereço do cliente
        if (empty($data['local_instalacao'])) {
            $cliente = Cliente::findOrFail($data['cliente_id']);

            $endereco = trim(sprintf(
                '%s, %s - %s, %s/%s',
                $cliente->rua ?? '',
                $cliente->numero ?? '',
                $cliente->bairro ?? '',
                $cliente->cidade ?? '',
                $cliente->uf ?? ''
            ));

            $data['local_instalacao'] = $endereco !== ',  - , /' ? $endereco : null;
        }

        if ($data['status'] === 'aberto') {
            $data['hora_deslocamento'] = now();
        }

        Servico::create($data);

        return redirect()->route('admin.servicos.index')
            ->with('success', 'Serviço criado com sucesso!');
    }

    public function show(Servico $servico)
    {
        $this->ensureCanAccess($servico);
        $servico->load(['cliente', 'colaborador', 'materiais']);
        $routeBase = $this->routeBase();

        return view('admin.servicos.show', compact('servico', 'routeBase'));
    }

    public function updateStatus(Request $request, Servico $servico)
    {
        $this->ensureCanAccess($servico);

        $novoStatus = $request->validate([
            'status' => ['required', 'in:aberto,em_execucao,finalizado'],
        ])['status'];

        $statusAtual = $servico->status;

        $permitido = match ($statusAtual) {
            'agendado' => $novoStatus === 'aberto',
            'aberto' => $novoStatus === 'em_execucao',
            'em_execucao' => $novoStatus === 'finalizado',
            default => false,
        };

        if (!$permitido) {
            return back()->with('success', 'Status já atualizado ou transição inválida.');
        }

        $agora = now();

        // AGENDADO -> ABERTO
        if ($novoStatus === 'aberto') {
            $servico->status = 'aberto';
            $servico->hora_deslocamento = $servico->hora_deslocamento ?? $agora;
            $servico->save();

            return back()->with('success', 'Serviço aberto!');
        }

        // ABERTO -> EM_EXECUCAO
        if ($novoStatus === 'em_execucao') {
            $servico->status = 'em_execucao';
            $servico->hora_execucao = $servico->hora_execucao ?? $agora;

            if ($servico->hora_deslocamento) {
                $servico->tempo_deslocamento_min = $servico->hora_deslocamento->diffInMinutes($servico->hora_execucao);
            }

            $servico->save();

            return back()->with('success', 'Execução iniciada!');
        }

        /**
         * EM_EXECUCAO -> FINALIZADO
         * - Baixa estoque uma única vez
         * - Preenche data_finalizacao (pro Painel SaaS)
         * - Salva hora_finalizado como TIME
         */
        if ($novoStatus === 'finalizado') {
            try {
                DB::transaction(function () use ($servico, $agora) {

                    $servicoLocked = Servico::where('id', $servico->id)->lockForUpdate()->first();

                    // Se estoque já foi baixado, só finaliza (idempotente)
                    if ($servicoLocked->estoque_baixado_em !== null) {
                        $servicoLocked->status = 'finalizado';

                        // TIME
                        $servicoLocked->hora_finalizado = $servicoLocked->hora_finalizado ?? $agora->format('H:i:s');
                        // DATETIME (dashboard)
                        $servicoLocked->data_finalizacao = $servicoLocked->data_finalizacao ?? $agora;

                        if ($servicoLocked->hora_execucao) {
                            $servicoLocked->tempo_servico_min =
                                $servicoLocked->hora_execucao->diffInMinutes($agora);
                        }

                        $servicoLocked->save();
                        return;
                    }

                    // Materiais lançados
                    $materiaisLancados = $servicoLocked->materiais()->get();

                    if ($materiaisLancados->isNotEmpty()) {
                        $materiaisDB = Material::whereIn('id', $materiaisLancados->pluck('id'))
                            ->lockForUpdate()
                            ->get()
                            ->keyBy('id');

                        // Validação de estoque
                        foreach ($materiaisLancados as $m) {
                            $mat = $materiaisDB->get($m->id);
                            $qtdUsada = (float) $m->pivot->quantidade_usada;

                            if (!$mat) {
                                throw new \Exception("Material inválido no lançamento (ID: {$m->id}).");
                            }

                            if ($qtdUsada > (float) $mat->quantidade) {
                                throw new \Exception(
                                    "Estoque insuficiente para {$mat->equipamento} - {$mat->marca}. " .
                                    "Estoque: {$mat->quantidade} {$mat->unidade} | Necessário: {$qtdUsada} {$mat->unidade}"
                                );
                            }
                        }

                        // Baixa estoque
                        foreach ($materiaisLancados as $m) {
                            $mat = $materiaisDB->get($m->id);
                            $qtdUsada = (float) $m->pivot->quantidade_usada;

                            $mat->quantidade = (float) $mat->quantidade - $qtdUsada;
                            $mat->save();
                        }
                    }

                    // Finaliza
                    $servicoLocked->status = 'finalizado';

                    // TIME
                    $servicoLocked->hora_finalizado = $servicoLocked->hora_finalizado ?? $agora->format('H:i:s');
                    // DATETIME
                    $servicoLocked->data_finalizacao = $servicoLocked->data_finalizacao ?? $agora;

                    if ($servicoLocked->hora_execucao) {
                        $servicoLocked->tempo_servico_min =
                            $servicoLocked->hora_execucao->diffInMinutes($agora);
                    }

                    $servicoLocked->estoque_baixado_em = $servicoLocked->estoque_baixado_em ?? $agora;
                    $servicoLocked->save();
                });

                return back()->with('success', 'Serviço finalizado! Estoque baixado com sucesso.');
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('success', 'Nada para atualizar.');
    }

    public function destroy(Servico $servico)
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403);
        }

        $servico->delete();

        return redirect()->route('admin.servicos.index')
            ->with('success', 'Serviço excluído com sucesso!');
    }

    public function pdf(Servico $servico)
    {
        $this->ensureCanAccess($servico);

        // Garante cliente carregado (se tiver relacionamento)
        $servico->load(['cliente']);

        // Caminho da logo (a mesma do sidebar)
        $logoPath = public_path('img/jf-logo.jpeg');

        // Converte imagem para base64 (DomPDF fica 100% confiável assim)
        $logoBase64 = null;
        if (File::exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('admin.servicos.pdf', [
            'servico' => $servico,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('servico-'.$servico->id.'.pdf');
    }

}
