<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Servico;
use App\Models\Material;

class AdminController extends Controller
{
    public function home(Request $request)
{
    $periodo = $request->get('periodo', 'mes'); // 'semana' | 'mes'

    $inicio = $periodo === 'semana'
        ? now()->startOfWeek()
        : now()->startOfMonth();

    $fim = $periodo === 'semana'
        ? now()->endOfWeek()
        : now()->endOfMonth();

    // ✅ Finalizados no período (usa data_finalizacao)
    $finalizados = Servico::where('status', 'finalizado')
        ->whereBetween('data_finalizacao', [$inicio, $fim])
        ->count();

    // ✅ Agendados no período (usa data + hora_prevista)
    $agendados = Servico::where('status', 'agendado')
        ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
        ->count();

    // ✅ Atrasados: agendado com datetime do agendamento < agora
    $atrasados = Servico::where('status', 'agendado')
        ->whereRaw("STR_TO_DATE(CONCAT(`data`, ' ', `hora_prevista`), '%Y-%m-%d %H:%i:%s') < ?", [now()->format('Y-m-d H:i:s')])
        ->count();

    // ✅ Materiais com estoque baixo/em falta
    $estoqueBaixoCount = Material::whereColumn('quantidade', '<=', 'estoque_minimo')->count();

    // ✅ Próximos agendados (hoje/amanhã + próximos 7 dias)
    $proximos = Servico::where('status', 'agendado')
        ->whereBetween('data', [now()->toDateString(), now()->addDays(7)->toDateString()])
        ->orderBy('data')
        ->orderBy('hora_prevista')
        ->limit(10)
        ->get();

    $estoqueBaixo = Material::whereColumn('quantidade', '<=', 'estoque_minimo')
        ->orderBy('quantidade')
        ->limit(10)
        ->get();

    // ✅ Gráfico por dia (Agendados x Finalizados)
    $labels = [];
    $serieAgendados = [];
    $serieFinalizados = [];

    $cursor = $inicio->copy();
    while ($cursor->lte($fim)) {
        $labels[] = $cursor->format('d/m');

        $serieAgendados[] = Servico::where('status', 'agendado')
            ->whereDate('data', $cursor->toDateString())
            ->count();

        $serieFinalizados[] = Servico::where('status', 'finalizado')
            ->whereDate('data_finalizacao', $cursor->toDateString())
            ->count();

        $cursor->addDay();
    }

    return view('admin.home', compact(
        'periodo',
        'inicio',
        'fim',
        'finalizados',
        'agendados',
        'atrasados',
        'estoqueBaixoCount',
        'proximos',
        'estoqueBaixo',
        'labels',
        'serieAgendados',
        'serieFinalizados'
    ));
}

}
