@extends('layouts.adminlte')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios')

@section('content')

<div class="d-flex justify-content-end mb-2">
    <a href="{{ route('admin.relatorios.pdf', request()->query()) }}" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Exportar PDF
    </a>
</div>

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
    </div>

    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-2 mb-2">
                <label>Período</label>
                <select name="periodo" class="form-control">
                    <option value="semana" {{ $periodo==='semana'?'selected':'' }}>Semana</option>
                    <option value="mes" {{ $periodo==='mes'?'selected':'' }}>Mês</option>
                    <option value="ano" {{ $periodo==='ano'?'selected':'' }}>Ano</option>
                    <option value="personalizado" {{ $periodo==='personalizado'?'selected':'' }}>Personalizado</option>
                </select>
            </div>

            <div class="col-md-2 mb-2">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="todos" {{ $status==='todos'?'selected':'' }}>Todos</option>
                    <option value="agendado" {{ $status==='agendado'?'selected':'' }}>Agendado</option>
                    <option value="aberto" {{ $status==='aberto'?'selected':'' }}>Aberto</option>
                    <option value="em_execucao" {{ $status==='em_execucao'?'selected':'' }}>Em execução</option>
                    <option value="finalizado" {{ $status==='finalizado'?'selected':'' }}>Finalizado</option>
                    <option value="cancelado" {{ $status==='cancelado'?'selected':'' }}>Cancelado</option>
                </select>
            </div>

            <div class="col-md-2 mb-2">
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

            <div class="col-md-2 mb-2">
                <label>Tipo</label>
                <select name="tipo_servico" class="form-control">
                    <option value="todos" {{ $tipoServico==='todos'?'selected':'' }}>Todos</option>
                    <option value="instalacao" {{ $tipoServico==='instalacao'?'selected':'' }}>Instalação</option>
                    <option value="manutencao" {{ $tipoServico==='manutencao'?'selected':'' }}>Manutenção</option>
                </select>
            </div>

            <div class="col-md-2 mb-2">
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

            <div class="col-md-2 mb-2">
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

            <div class="col-md-2 mb-2">
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

            <div class="col-md-3 mb-2">
                <label>Início</label>
                <input type="date" name="inicio" class="form-control" value="{{ $inicio }}"
                       {{ $periodo!=='personalizado'?'disabled':'' }}>
            </div>

            <div class="col-md-3 mb-2">
                <label>Fim</label>
                <input type="date" name="fim" class="form-control" value="{{ $fim }}"
                       {{ $periodo!=='personalizado'?'disabled':'' }}>
            </div>

            <div class="col-md-6 mb-2 d-flex align-items-end">
                <button class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Aplicar
                </button>

                <a href="{{ route('admin.relatorios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalRegistros }}</h3>
                <p>Serviços no período</p>
            </div>
            <div class="icon"><i class="fas fa-list"></i></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalFinalizado }}</h3>
                <p>Finalizados</p>
            </div>
            <div class="icon"><i class="fas fa-check"></i></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalExecucao }}</h3>
                <p>Em execução</p>
            </div>
            <div class="icon"><i class="fas fa-tools"></i></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalCancelado }}</h3>
                <p>Cancelados</p>
            </div>
            <div class="icon"><i class="fas fa-times"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Serviços por dia da semana</h3>
            </div>
            <div class="card-body">
                <canvas id="graficoDias" height="95"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Serviços</h3>
                <div class="card-tools text-muted">
                    Período: {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($fim)->format('d/m/Y') }}
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
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
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($s->data)->format('d/m/Y') }}</td>
                                <td>{{ $s->cliente->nome ?? '-' }}</td>
                                <td>{{ $s->tipo_servico_label }}</td>
                                <td>{{ $s->local_instalacao ?? '-' }}</td>
                                <td>
                                    @if($s->materiais->count())
                                        <small>
                                            @foreach($s->materiais->take(2) as $mat)
                                                {{ $mat->nome }} ({{ rtrim(rtrim(number_format($mat->pivot->quantidade,2,',','.'),'0'),',') }})@if(!$loop->last), @endif
                                            @endforeach
                                            @if($s->materiais->count() > 2)
                                                +{{ $s->materiais->count() - 2 }}
                                            @endif
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ strtoupper(str_replace('_',' ',$s->status)) }}</td>
                                <td class="text-right">R$ {{ number_format($s->valor_total ?? $s->total ?? $s->preco ?? $s->orcamento ?? 0, 2, ',', '.') }}</td>
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

    <div class="col-md-4">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>R$ {{ number_format($totalValor ?? 0, 2, ',', '.') }}</h3>
                <p>Valor total (período)</p>
            </div>
            <div class="icon"><i class="fas fa-coins"></i></div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top clientes</h3>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th class="text-right">Qtd</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topClientes as $row)
                            <tr>
                                <td>{{ $row->cliente->nome ?? '—' }}</td>
                                <td class="text-right">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted p-3">Sem dados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labelsDias = @json($labelsDias);
    const dadosDias  = @json($dadosDias);

    const ctx = document.getElementById('graficoDias');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labelsDias,
            datasets: [{
                label: 'Instalações',
                data: dadosDias
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>

@endsection
