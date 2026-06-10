@extends('layouts.adminlte')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios')

@section('content')

@php
    $periodoTexto = \Carbon\Carbon::parse($inicio)->format('d/m/Y') . ' a ' . \Carbon\Carbon::parse($fim)->format('d/m/Y');
    $statusBadge = [
        'agendado' => 'info',
        'aberto' => 'secondary',
        'em_deslocamento' => 'primary',
        'em_execucao' => 'warning',
        'finalizado' => 'success',
        'cancelado' => 'danger',
    ];
@endphp

<style>
    .report-page {
        color: #071827;
    }
    .report-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .report-title h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
    }
    .report-title span {
        color: #667085;
        font-size: 14px;
    }
    .filter-panel {
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(7, 24, 39, .05);
    }
    .filter-panel .card-header {
        background: #fff;
        border-bottom: 1px solid #edf0f5;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #e6eaf0;
        border-radius: 8px;
        padding: 16px;
        min-height: 132px;
        box-shadow: 0 10px 24px rgba(7, 24, 39, .05);
    }
    .kpi-card .label {
        color: #667085;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .kpi-card .value {
        font-size: 28px;
        font-weight: 800;
        margin-top: 8px;
        line-height: 1.15;
    }
    .kpi-card .note {
        color: #667085;
        font-size: 13px;
        margin-top: 8px;
    }
    .metric-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(244, 194, 26, .18);
        color: #071827;
        margin-bottom: 10px;
    }
    .report-card {
        border: 1px solid #e6eaf0;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(7, 24, 39, .05);
    }
    .report-card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf0f5;
    }
    .report-card .card-title {
        font-weight: 800;
    }
    .chart-box {
        position: relative;
        height: 240px;
    }
    .chart-box-sm {
        position: relative;
        height: 180px;
    }
    .chart-box-xs {
        position: relative;
        height: 150px;
    }
    .insight-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .insight-list li {
        display: flex;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #edf0f5;
        color: #344054;
    }
    .insight-list li:last-child {
        border-bottom: 0;
    }
    .rank-number {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3f5f8;
        font-weight: 800;
        color: #071827;
    }
    .type-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }
    .type-summary-item {
        background: #f8fafc;
        border: 1px solid #edf0f5;
        border-radius: 8px;
        padding: 10px;
    }
    .type-summary-item strong {
        display: block;
        font-size: 20px;
        line-height: 1.1;
    }
    .type-summary-item span {
        color: #667085;
        font-size: 12px;
    }
    .table td,
    .table th {
        vertical-align: middle;
    }
    @media (max-width: 767.98px) {
        .report-title h2 {
            font-size: 20px;
        }
        .kpi-card .value {
            font-size: 24px;
        }
    }
</style>

<div class="report-page">
    <div class="report-toolbar">
        <div class="report-title">
            <h2>Visão gerencial dos serviços</h2>
            <span>Período analisado: {{ $periodoTexto }}</span>
        </div>

        <a href="{{ route('admin.relatorios.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
    </div>

    <div class="card filter-panel">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de análise</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="row" id="relatorioFiltros">
                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Período</label>
                    <select name="periodo" id="periodo" class="form-control">
                        <option value="semana" {{ $periodo==='semana'?'selected':'' }}>Semana</option>
                        <option value="mes" {{ $periodo==='mes'?'selected':'' }}>Mês</option>
                        <option value="ano" {{ $periodo==='ano'?'selected':'' }}>Ano</option>
                        <option value="personalizado" {{ $periodo==='personalizado'?'selected':'' }}>Personalizado</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="todos" {{ $status==='todos'?'selected':'' }}>Todos</option>
                        <option value="agendado" {{ $status==='agendado'?'selected':'' }}>Agendado</option>
                        <option value="aberto" {{ $status==='aberto'?'selected':'' }}>Aberto</option>
                        <option value="em_deslocamento" {{ $status==='em_deslocamento'?'selected':'' }}>Em deslocamento</option>
                        <option value="em_execucao" {{ $status==='em_execucao'?'selected':'' }}>Em execução</option>
                        <option value="finalizado" {{ $status==='finalizado'?'selected':'' }}>Finalizado</option>
                        <option value="cancelado" {{ $status==='cancelado'?'selected':'' }}>Cancelado</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Tipo</label>
                    <select name="tipo_servico" class="form-control">
                        <option value="todos" {{ $tipoServico==='todos'?'selected':'' }}>Todos</option>
                        <option value="instalacao" {{ $tipoServico==='instalacao'?'selected':'' }}>Instalação</option>
                        <option value="manutencao" {{ $tipoServico==='manutencao'?'selected':'' }}>Manutenção</option>
                        <option value="orcamento" {{ $tipoServico==='orcamento'?'selected':'' }}>Orçamento</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 mb-2">
                    <label>Cliente</label>
                    <select name="cliente_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 mb-2">
                    <label>Local</label>
                    <select name="local" class="form-control">
                        <option value="">Todos</option>
                        @foreach($locais as $l)
                            <option value="{{ $l }}" {{ request('local') == $l ? 'selected' : '' }}>
                                {{ $l }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 mb-2">
                    <label>Material</label>
                    <select name="material_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($materiais as $m)
                            <option value="{{ $m->id }}" {{ request('material_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->equipamento }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Dia da semana</label>
                    <select name="dia_semana" class="form-control">
                        <option value="">Todos</option>
                        <option value="2" {{ request('dia_semana')=='2'?'selected':'' }}>Segunda</option>
                        <option value="3" {{ request('dia_semana')=='3'?'selected':'' }}>Terça</option>
                        <option value="4" {{ request('dia_semana')=='4'?'selected':'' }}>Quarta</option>
                        <option value="5" {{ request('dia_semana')=='5'?'selected':'' }}>Quinta</option>
                        <option value="6" {{ request('dia_semana')=='6'?'selected':'' }}>Sexta</option>
                        <option value="7" {{ request('dia_semana')=='7'?'selected':'' }}>Sábado</option>
                        <option value="1" {{ request('dia_semana')=='1'?'selected':'' }}>Domingo</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Início</label>
                    <input type="date" name="inicio" class="form-control periodo-personalizado" value="{{ $inicio }}">
                </div>

                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Fim</label>
                    <input type="date" name="fim" class="form-control periodo-personalizado" value="{{ $fim }}">
                </div>

                <div class="col-lg-3 col-md-12 mb-2 d-flex align-items-end">
                    <button class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Aplicar
                    </button>
                    <a href="{{ route('admin.relatorios.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <span class="metric-icon"><i class="fas fa-clipboard-list"></i></span>
                <div class="label">Serviços no período</div>
                <div class="value">{{ $totalRegistros }}</div>
                <div class="note">
                    {{ $clientesAtendidos }} cliente(s) atendido(s)
                    @if($totalAtrasado > 0)
                        · {{ $totalAtrasado }} atrasado(s)
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <span class="metric-icon"><i class="fas fa-check-circle"></i></span>
                <div class="label">Taxa de conclusão</div>
                <div class="value">{{ number_format($taxaConclusao, 1, ',', '.') }}%</div>
                <div class="note">{{ $totalFinalizado }} finalizado(s), {{ $totalPendente }} em aberto</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <span class="metric-icon"><i class="fas fa-coins"></i></span>
                <div class="label">Valor no período</div>
                <div class="value">R$ {{ number_format($totalValor ?? 0, 2, ',', '.') }}</div>
                <div class="note">Ticket médio: R$ {{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card">
                <span class="metric-icon"><i class="fas fa-stopwatch"></i></span>
                <div class="label">Tempo médio</div>
                <div class="value">{{ $tempoMedioServicoTexto }}</div>
                <div class="note">Deslocamento médio: {{ $tempoMedioDeslocamentoTexto }}</div>
            </div>
        </div>
    </div>

    <div class="row align-items-start">
        <div class="col-xl-8">
            <div class="card report-card">
                <div class="card-header">
                    <h3 class="card-title">Demanda por dia da semana</h3>
                    <div class="card-tools text-muted">Ajuda a planejar equipe e agenda</div>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="graficoDias"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card report-card">
                        <div class="card-header">
                            <h3 class="card-title">Clientes com mais serviços</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 52px;">#</th>
                                        <th>Cliente</th>
                                        <th class="text-right">Serviços</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topClientes as $index => $row)
                                        <tr>
                                            <td><span class="rank-number">{{ $index + 1 }}</span></td>
                                            <td>{{ $row->cliente->nome ?? '-' }}</td>
                                            <td class="text-right font-weight-bold">{{ $row->total }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted p-4">Sem clientes no período.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card report-card">
                        <div class="card-header">
                            <h3 class="card-title">Locais com mais demanda</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 52px;">#</th>
                                        <th>Local</th>
                                        <th class="text-right">Serviços</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topLocais as $index => $row)
                                        <tr>
                                            <td><span class="rank-number">{{ $index + 1 }}</span></td>
                                            <td>{{ $row->local_instalacao }}</td>
                                            <td class="text-right font-weight-bold">{{ $row->total }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted p-4">Sem locais no período.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card report-card">
                <div class="card-header">
                    <h3 class="card-title">Leitura rápida</h3>
                </div>
                <div class="card-body">
                    <ul class="insight-list">
                        @foreach($insights as $insight)
                            <li>
                                <i class="fas fa-chart-line text-warning mt-1"></i>
                                <span>{{ $insight }}</span>
                            </li>
                        @endforeach
                        <li>
                            <i class="fas fa-ban text-danger mt-1"></i>
                            <span>Cancelamento: {{ number_format($taxaCancelamento, 1, ',', '.') }}% no período.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card report-card">
                <div class="card-header">
                    <h3 class="card-title">Serviços por tipo</h3>
                </div>
                <div class="card-body">
                    <div class="type-summary">
                        <div class="type-summary-item">
                            <strong>{{ $tipoInstalacao }}</strong>
                            <span>Instalações · {{ number_format($percentualInstalacao, 1, ',', '.') }}%</span>
                        </div>
                        <div class="type-summary-item">
                            <strong>{{ $tipoManutencao }}</strong>
                            <span>Manutenções · {{ number_format($percentualManutencao, 1, ',', '.') }}%</span>
                        </div>
                        <div class="type-summary-item">
                            <strong>{{ $tipoOrcamento }}</strong>
                            <span>Orçamentos · {{ number_format($percentualOrcamento, 1, ',', '.') }}%</span>
                        </div>
                    </div>
                    <div class="chart-box-xs">
                        <canvas id="graficoTipos"></canvas>
                    </div>
                </div>
            </div>

            <div class="card report-card">
                <div class="card-header">
                    <h3 class="card-title">Status operacional</h3>
                </div>
                <div class="card-body">
                    <div class="chart-box-sm">
                        <canvas id="graficoStatus"></canvas>
                    </div>
                </div>
            </div>

            <div class="card report-card">
                <div class="card-header">
                    <h3 class="card-title">Materiais mais usados</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 52px;">#</th>
                                <th>Material</th>
                                <th class="text-right">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMateriais as $index => $material)
                                <tr>
                                    <td><span class="rank-number">{{ $index + 1 }}</span></td>
                                    <td>
                                        <strong>{{ $material['nome'] }}</strong>
                                        <div class="text-muted small">{{ $material['servicos'] }} serviço(s)</div>
                                    </td>
                                    <td class="text-right">
                                        {{ rtrim(rtrim(number_format($material['quantidade'], 2, ',', '.'), '0'), ',') }}
                                        {{ $material['unidade'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted p-4">Nenhum material lançado no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card report-card">
        <div class="card-header">
            <h3 class="card-title">Serviços detalhados</h3>
            <div class="card-tools text-muted">{{ $servicos->total() }} registro(s)</div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Local</th>
                        <th>Materiais</th>
                        <th>Status</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicos as $s)
                        @php
                            $valorServico = $s->valor ?? $s->valor_total ?? $s->total ?? $s->preco ?? $s->orcamento ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $s->data ? \Carbon\Carbon::parse($s->data)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $s->cliente->nome ?? '-' }}</td>
                            <td>{{ $s->tipo_servico_label }}</td>
                            <td>{{ $s->local_instalacao ?? '-' }}</td>
                            <td>
                                @if($s->materiais->count())
                                    <small>
                                        @foreach($s->materiais->take(2) as $mat)
                                            {{ $mat->equipamento ?? $mat->nome ?? '-' }}
                                            ({{ rtrim(rtrim(number_format($mat->pivot->quantidade_usada ?? 0, 2, ',', '.'), '0'), ',') }})
                                            @if(!$loop->last), @endif
                                        @endforeach
                                        @if($s->materiais->count() > 2)
                                            +{{ $s->materiais->count() - 2 }}
                                        @endif
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $statusBadge[$s->status] ?? 'secondary' }}">
                                    {{ strtoupper(str_replace('_',' ', $s->status)) }}
                                </span>
                            </td>
                            <td class="text-right">R$ {{ number_format($valorServico, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted p-4">Nenhum registro encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $servicos->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const periodoSelect = document.getElementById('periodo');
    const camposPeriodo = document.querySelectorAll('.periodo-personalizado');

    function atualizarCamposPeriodo() {
        const custom = periodoSelect.value === 'personalizado';
        camposPeriodo.forEach((campo) => {
            campo.disabled = !custom;
        });
    }

    periodoSelect.addEventListener('change', atualizarCamposPeriodo);
    atualizarCamposPeriodo();

    const chartColors = {
        yellow: '#F4C21A',
        blue: '#071827',
        green: '#28a745',
        red: '#dc3545',
        cyan: '#17a2b8',
        gray: '#6c757d',
        orange: '#fd7e14'
    };

    new Chart(document.getElementById('graficoDias'), {
        type: 'bar',
        data: {
            labels: @json($labelsDias),
            datasets: [{
                label: 'Serviços',
                data: @json($dadosDias),
                backgroundColor: chartColors.yellow,
                borderColor: chartColors.blue,
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('graficoTipos'), {
        type: 'doughnut',
        data: {
            labels: @json($tipoLabels),
            datasets: [{
                data: @json($tipoDados),
                backgroundColor: [chartColors.yellow, chartColors.blue, chartColors.orange, chartColors.gray],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '62%'
        }
    });

    new Chart(document.getElementById('graficoStatus'), {
        type: 'bar',
        data: {
            labels: @json($statusLabels),
            datasets: [{
                label: 'Serviços',
                data: @json($statusDados),
                backgroundColor: [
                    chartColors.cyan,
                    chartColors.gray,
                    chartColors.orange,
                    chartColors.yellow,
                    chartColors.green,
                    chartColors.red
                ],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 } },
                y: { grid: { display: false } }
            }
        }
    });
</script>

@endsection
