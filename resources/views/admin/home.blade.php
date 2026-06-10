@extends('layouts.adminlte')

@section('title', 'Painel')
@section('page-title', 'Painel')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-2">
        <!-- <h1 class="m-0">Painel</h1> -->

        <form method="GET" class="d-flex gap-2">
            <select name="periodo" class="form-control" onchange="this.form.submit()">
                <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Semana</option>
                <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Mês</option>
            </select>
        </form>
    </div>

    <p class="text-muted mb-3">
        Período: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}
    </p>

    <div class="row">

        {{-- KPIs --}}
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $finalizados }}</h3>
                    <p>Serviços finalizados</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $agendados }}</h3>
                    <p>Serviços agendados</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $atrasados }}</h3>
                    <p>Agendados atrasados</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $estoqueBaixoCount }}</h3>
                    <p>Itens com estoque baixo</p>
                </div>
                <div class="icon"><i class="fas fa-box-open"></i></div>
            </div>
        </div>

        {{-- GRÁFICO --}}
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Resumo do período</h3>
                </div>
                <div class="card-body">
                    <canvas id="grafResumo" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- PRÓXIMOS AGENDADOS --}}
        <div class="col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Próximos agendados (7 dias)</h3>
                </div>
                <div class="card-body p-0">
                    @if($proximos->count() === 0)
                        <div class="p-3 text-muted">Nenhum serviço agendado para os próximos dias.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($proximos as $s)
                                @php
                                    $d = \Carbon\Carbon::parse($s->data);
                                    $badge = $d->isToday()
                                        ? 'badge-danger'
                                        : ($d->isTomorrow() ? 'badge-warning' : 'badge-secondary');
                                    $txt = $d->isToday()
                                        ? 'HOJE'
                                        : ($d->isTomorrow() ? 'AMANHÃ' : 'EM BREVE');
                                @endphp

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $s->tipo_servico_label }} #{{ $s->id }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $d->format('d/m/Y') }} {{ $s->hora_prevista }}
                                        </small>
                                    </div>

                                    <span class="badge {{ $badge }}">{{ $txt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- ESTOQUE BAIXO --}}
        <div class="col-12">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Estoque baixo / em falta</h3>
                </div>
                <div class="card-body p-0">
                    @if($estoqueBaixo->count() === 0)
                        <div class="p-3 text-muted">Nenhum item com estoque baixo.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th class="text-center">Estoque</th>
                                        <th class="text-center">Mínimo</th>
                                        <th class="text-right">Situação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estoqueBaixo as $m)
                                        @php
                                            $ok = (float) $m->quantidade > 0;
                                            $sit = $ok ? 'Baixo' : 'Em falta';
                                            $class = $ok ? 'text-warning' : 'text-danger';
                                        @endphp
                                        <tr>
                                            <td>{{ $m->nome ?? $m->equipamento ?? ('#' . $m->id) }}</td>
                                            <td class="text-center">{{ $m->quantidade }}</td>
                                            <td class="text-center">{{ $m->estoque_minimo }}</td>
                                            <td class="text-right font-weight-bold {{ $class }}">{{ $sit }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('grafResumo');

        const labels = @json($labels);
        const agendados = @json($serieAgendados);
        const finalizados = @json($serieFinalizados);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Agendados', data: agendados, tension: 0.3 },
                    { label: 'Finalizados', data: finalizados, tension: 0.3 },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endsection
