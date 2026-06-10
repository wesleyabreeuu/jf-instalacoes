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

        $totalValor = $campoValor ? (clone $query)->sum($campoValor) : 0;

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

        // Top clientes
        $topClientes = (clone $query)
            ->selectRaw('cliente_id, COUNT(*) as total')
            ->groupBy('cliente_id')
            ->orderByDesc('total')
            ->with('cliente')
            ->limit(5)
            ->get();

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
            'totalValor',
            'labelsDias',
            'dadosDias',
            'topClientes',
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
