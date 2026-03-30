<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use App\Models\Material;
use Illuminate\Http\Request;

class ServicoMaterialController extends Controller
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

    public function create(Servico $servico)
    {
        $this->ensureCanAccess($servico);
        $routeBase = $this->routeBase();

        // Só permite lançar materiais quando estiver em execução
        if ($servico->status !== 'em_execucao') {
            return redirect()
                ->route("{$routeBase}.show", $servico->id)
                ->with('success', 'O serviço precisa estar EM EXECUÇÃO para lançar materiais.');
        }

        $materiais = Material::where('ativo', 1)
            ->orderBy('equipamento')
            ->orderBy('marca')
            ->get();

        $lancados = $servico->materiais()->get();

        return view('admin.materiais.usar_no_servico', compact('servico', 'materiais', 'lancados', 'routeBase'));
    }

    public function store(Request $request, Servico $servico)
    {
        $this->ensureCanAccess($servico);
        $routeBase = $this->routeBase();

        // Só permite salvar quando estiver em execução
        if ($servico->status !== 'em_execucao') {
            return redirect()
                ->route("{$routeBase}.show", $servico->id)
                ->with('success', 'O serviço precisa estar EM EXECUÇÃO para salvar materiais.');
        }

        $data = $request->validate([
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.material_id' => ['required', 'integer', 'exists:materiais,id'],
            'itens.*.quantidade_usada' => ['required', 'numeric', 'min:0.01'],
        ]);

        // 1) Agrupa itens repetidos (se o usuário selecionar o mesmo material mais de 1x)
        $agrupados = [];
        foreach ($data['itens'] as $item) {
            $mid = (int) $item['material_id'];
            $qtd = (float) $item['quantidade_usada'];

            if (!isset($agrupados[$mid])) {
                $agrupados[$mid] = 0;
            }
            $agrupados[$mid] += $qtd;
        }

        // 2) Valida estoque
        $materiais = Material::whereIn('id', array_keys($agrupados))->get()->keyBy('id');

        foreach ($agrupados as $materialId => $qtdUsada) {
            $mat = $materiais->get($materialId);

            if (!$mat) {
                return back()->withErrors(['itens' => 'Material inválido.'])->withInput();
            }

            if ($qtdUsada > (float) $mat->quantidade) {
                return back()
                    ->withErrors([
                        'itens' => "Quantidade usada maior que o estoque para: {$mat->equipamento} - {$mat->marca}. Estoque: {$mat->quantidade} {$mat->unidade}"
                    ])
                    ->withInput();
            }
        }

        // 3) Monta formato do sync
        $syncData = [];
        foreach ($agrupados as $materialId => $qtdUsada) {
            $syncData[$materialId] = ['quantidade_usada' => $qtdUsada];
        }

        // Substitui tudo pelos itens enviados
        $servico->materiais()->sync($syncData);

        return redirect()
            ->route("{$routeBase}.show", $servico->id)
            ->with('success', 'Materiais do serviço salvos com sucesso!');
    }
}
