<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Servico;
use App\Models\Cliente;
use App\Models\Material;

class RelatorioController extends Controller
{
    private function percentual(int|float $parte, int|float $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($parte / $total) * 100, 1);
    }

    private function minutosParaTexto(?float $minutos): string
    {
        if ($minutos === null || $minutos <= 0) {
            return '-';
        }

        $minutos = (int) round($minutos);
        $horas = intdiv($minutos, 60);
        $resto = $minutos % 60;

        if ($horas <= 0) {
            return $resto . 'min';
        }

        return $horas . 'h ' . str_pad((string) $resto, 2, '0', STR_PAD_LEFT) . 'min';
    }

    private function campoValorServico(): ?string
    {
        $possiveis = ['valor', 'valor_total', 'total', 'preco', 'orcamento', 'preco_total', 'valor_servico'];
        foreach ($possiveis as $c) {
            if (Schema::hasColumn('servicos', $c)) return $c;
        }
        return null;
    }

    private function aplicarFiltros(Request $request)
    {
        $periodo = $request->get('periodo', 'mes'); // semana | mes | ano | personalizado
        $status  = $request->get('status', 'todos');
        $tipoServico = $request->get('tipo_servico', 'todos');

        // Datas padrão
        $inicio = now()->startOfMonth()->toDateString();
        $fim    = now()->endOfMonth()->toDateString();

        if ($periodo === 'semana') {
            $inicio = now()->startOfWeek()->toDateString();
            $fim    = now()->endOfWeek()->toDateString();
        } elseif ($periodo === 'ano') {
            $inicio = now()->startOfYear()->toDateString();
            $fim    = now()->endOfYear()->toDateString();
        } elseif ($periodo === 'personalizado') {
            $inicio = $request->get('inicio', $inicio);
            $fim    = $request->get('fim', $fim);
        }

        $query = Servico::query()
            ->with(['cliente', 'materiais'])
            ->whereDate('data', '>=', $inicio)
            ->whereDate('data', '<=', $fim);

        // Status
        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        if ($tipoServico !== 'todos') {
            $query->where('tipo_servico', $tipoServico);
        }

        // Cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Local de instalação
        if ($request->filled('local')) {
            $query->where('local_instalacao', $request->local);
        }

        // Material
        if ($request->filled('material_id')) {
            $materialId = (int) $request->material_id;
            $query->whereHas('materiais', fn($q) => $q->where('materiais.id', $materialId));
        }

        // Dia da semana (MySQL DAYOFWEEK: 1=Dom,2=Seg,...7=Sáb)
        if ($request->filled('dia_semana')) {
            $query->whereRaw('DAYOFWEEK(`data`) = ?', [(int)$request->dia_semana]);
        }

        return [
            'query' => $query,
            'periodo' => $periodo,
            'status' => $status,
            'tipoServico' => $tipoServico,
            'inicio' => $inicio,
            'fim' => $fim,
        ];
    }

    public function index(Request $request)
    {
        $f = $this->aplicarFiltros($request);
        $query = $f['query'];

        $campoValor = $this->campoValorServico();

        // Listagem
        $servicos = (clone $query)
            ->orderBy('data', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Cards
        $totalRegistros  = (clone $query)->count();
        $totalFinalizado = (clone $query)->where('status', 'finalizado')->count();
        $totalCancelado  = (clone $query)->where('status', 'cancelado')->count();
        $totalExecucao   = (clone $query)->where('status', 'em_execucao')->count();
        $totalAgendado   = (clone $query)->where('status', 'agendado')->count();
        $totalAberto     = (clone $query)->where('status', 'aberto')->count();
        $totalPendente   = $totalAgendado + $totalAberto + $totalExecucao;

        $totalValor = $campoValor ? (clone $query)->sum($campoValor) : 0;
        $ticketMedio = $totalRegistros > 0 ? $totalValor / $totalRegistros : 0;
        $taxaConclusao = $this->percentual($totalFinalizado, $totalRegistros);
        $taxaCancelamento = $this->percentual($totalCancelado, $totalRegistros);
        $clientesAtendidos = (clone $query)->distinct('cliente_id')->count('cliente_id');

        $tempoMedioServico = (clone $query)
            ->whereNotNull('tempo_servico_min')
            ->avg('tempo_servico_min');

        $tempoMedioDeslocamento = (clone $query)
            ->whereNotNull('tempo_deslocamento_min')
            ->avg('tempo_deslocamento_min');

        $tempoMedioServicoTexto = $this->minutosParaTexto($tempoMedioServico ? (float) $tempoMedioServico : null);
        $tempoMedioDeslocamentoTexto = $this->minutosParaTexto($tempoMedioDeslocamento ? (float) $tempoMedioDeslocamento : null);

        // Gráfico: serviços por dia da semana (no período/filtros, exceto o filtro específico de dia)
        $queryDias = $this->aplicarFiltros(new Request(array_merge($request->query(), ['dia_semana' => null])))['query'];

        $dias = (clone $queryDias)
            ->selectRaw('DAYOFWEEK(`data`) as dow, COUNT(*) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow')
            ->toArray();

        // Montar ordem Seg..Dom pro gráfico
        $map = [
            2 => 'Seg', 3 => 'Ter', 4 => 'Qua', 5 => 'Qui', 6 => 'Sex', 7 => 'Sáb', 1 => 'Dom'
        ];
        $labelsDias = array_values($map);
        $dadosDias = [];
        foreach (array_keys($map) as $dow) {
            $dadosDias[] = (int) ($dias[$dow] ?? 0);
        }

        $statusLabels = ['Agendado', 'Aberto', 'Em execução', 'Finalizado', 'Cancelado'];
        $statusDados = [
            $totalAgendado,
            $totalAberto,
            $totalExecucao,
            $totalFinalizado,
            $totalCancelado,
        ];

        $tipoInstalacao = (clone $query)->where('tipo_servico', 'instalacao')->count();
        $tipoManutencao = (clone $query)->where('tipo_servico', 'manutencao')->count();
        $tipoNaoInformado = max(0, $totalRegistros - $tipoInstalacao - $tipoManutencao);

        $tipoLabels = ['Instalação', 'Manutenção'];
        $tipoDados = [$tipoInstalacao, $tipoManutencao];
        if ($tipoNaoInformado > 0) {
            $tipoLabels[] = 'Não informado';
            $tipoDados[] = $tipoNaoInformado;
        }

        // Top clientes
        $topClientes = (clone $query)
            ->selectRaw('cliente_id, COUNT(*) as total')
            ->groupBy('cliente_id')
            ->orderByDesc('total')
            ->with('cliente')
            ->limit(5)
            ->get();

        $topLocais = (clone $query)
            ->whereNotNull('local_instalacao')
            ->where('local_instalacao', '<>', '')
            ->selectRaw('local_instalacao, COUNT(*) as total')
            ->groupBy('local_instalacao')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $servicosParaMateriais = (clone $query)
            ->with('materiais')
            ->get();

        $topMateriais = $servicosParaMateriais
            ->flatMap(function ($servico) {
                return $servico->materiais->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'nome' => trim(($material->equipamento ?? '-') . ' ' . ($material->marca ?? '')),
                        'unidade' => $material->unidade ?? 'un',
                        'quantidade' => (float) ($material->pivot->quantidade_usada ?? 0),
                    ];
                });
            })
            ->groupBy('id')
            ->map(function ($linhas) {
                $primeiro = $linhas->first();

                return [
                    'nome' => $primeiro['nome'],
                    'unidade' => $primeiro['unidade'],
                    'quantidade' => $linhas->sum('quantidade'),
                    'servicos' => $linhas->count(),
                ];
            })
            ->sortByDesc('quantidade')
            ->take(5)
            ->values();

        $insights = [];
        if ($totalRegistros === 0) {
            $insights[] = 'Nenhum serviço encontrado para os filtros selecionados.';
        } else {
            $insights[] = $taxaConclusao >= 70
                ? 'A conclusão está saudável para o período filtrado.'
                : 'A conclusão está baixa; vale revisar serviços pendentes e gargalos de execução.';

            if ($taxaCancelamento > 10) {
                $insights[] = 'Cancelamentos acima de 10% podem indicar problema de agenda, orçamento ou alinhamento com cliente.';
            }

            if ($tipoInstalacao || $tipoManutencao) {
                $tipoPrincipal = $tipoInstalacao >= $tipoManutencao ? 'instalações' : 'manutenções';
                $insights[] = 'A maior demanda do período está em ' . $tipoPrincipal . '.';
            }
        }

        // Dados de filtro
        $clientes = Cliente::orderBy('nome')->get(['id','nome']);
        $materiais = Material::orderBy('equipamento')->get(['id','equipamento']);


        // Locais distintos (pra dropdown)
        $locais = Servico::query()
            ->whereNotNull('local_instalacao')
            ->where('local_instalacao', '<>', '')
            ->select('local_instalacao')
            ->distinct()
            ->orderBy('local_instalacao')
            ->pluck('local_instalacao');

        return view('admin.relatorios.index', array_merge($f, compact(
            'servicos',
            'totalRegistros',
            'totalFinalizado',
            'totalCancelado',
            'totalExecucao',
            'totalAgendado',
            'totalAberto',
            'totalPendente',
            'totalValor',
            'ticketMedio',
            'taxaConclusao',
            'taxaCancelamento',
            'clientesAtendidos',
            'tempoMedioServicoTexto',
            'tempoMedioDeslocamentoTexto',
            'labelsDias',
            'dadosDias',
            'statusLabels',
            'statusDados',
            'tipoLabels',
            'tipoDados',
            'topClientes',
            'topLocais',
            'topMateriais',
            'insights',
            'clientes',
            'materiais',
            'locais'
        )));
    }

    public function pdf(Request $request)
    {
        $f = $this->aplicarFiltros($request);
        $query = $f['query'];

        $campoValor = $this->campoValorServico();

        $servicos = (clone $query)
            ->orderBy('data', 'desc')
            ->get();

        $totalRegistros  = $servicos->count();
        $totalFinalizado = $servicos->where('status', 'finalizado')->count();
        $totalCancelado  = $servicos->where('status', 'cancelado')->count();
        $totalExecucao   = $servicos->where('status', 'em_execucao')->count();

        $totalValor = $campoValor ? $servicos->sum($campoValor) : 0;

        $pdf = Pdf::loadView('admin.relatorios.pdf', array_merge($f, compact(
            'servicos',
            'totalRegistros',
            'totalFinalizado',
            'totalCancelado',
            'totalExecucao',
            'totalValor'
        )))->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-servicos.pdf');
    }
}
