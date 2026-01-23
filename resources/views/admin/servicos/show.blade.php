@extends('layouts.adminlte')

@section('title', 'Serviço')
@section('page-title', 'Serviço #'.$servico->id)

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@php
    $statusColors = [
        'agendado' => 'info',
        'aberto' => 'secondary',
        'em_execucao' => 'warning',
        'finalizado' => 'success',
        'cancelado' => 'danger',
    ];

    // helper simples: minutos -> "HH:MM"
    $minToHHMM = function ($min) {
        if ($min === null) return '-';
        $min = (int) $min;
        $h = intdiv($min, 60);
        $m = $min % 60;
        return sprintf('%02d:%02d', $h, $m);
    };
@endphp

<div class="card">
    <div class="card-body">

        {{-- CABEÇALHO --}}
        <div class="mb-3">
            <h5><b>Cliente:</b> {{ $servico->cliente->nome ?? '-' }}</h5>

            {{-- ✅ NOVO: Local de instalação --}}
            <p class="mb-1"><b>Local de instalação:</b> {{ $servico->local_instalacao ?? '-' }}</p>

            <p class="mb-1"><b>Colaborador:</b> {{ $servico->colaborador->name ?? '-' }}</p>
            <p class="mb-1"><b>Data:</b> {{ $servico->data?->format('d/m/Y') ?? '-' }}</p>
            <p class="mb-0"><b>Hora prevista:</b> {{ $servico->hora_prevista ?? '-' }}</p>
        </div>

        <div class="mb-3">
            <span class="badge badge-{{ $statusColors[$servico->status] ?? 'secondary' }}" style="font-size: 14px;">
                STATUS: {{ strtoupper(str_replace('_',' ', $servico->status)) }}
            </span>
        </div>

        <hr>

        {{-- SEÇÃO DETALHES (SÓ QUANDO FINALIZADO) --}}
        @if($servico->status === 'finalizado')
            <h5 class="mb-3"><b>Detalhes do serviço finalizado</b></h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="small text-muted">Hora de abertura/deslocamento</div>
                    <div class="font-weight-bold">
                        {{ $servico->hora_deslocamento?->format('d/m/Y H:i') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Hora de início da execução</div>
                    <div class="font-weight-bold">
                        {{ $servico->hora_execucao?->format('d/m/Y H:i') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small text-muted">Hora de finalização</div>
                    <div class="font-weight-bold">
                        {{ $servico->hora_finalizado?->format('d/m/Y H:i') ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-warning mb-0">
                        <div class="small text-muted">Tempo de deslocamento</div>
                        <div style="font-size: 22px; font-weight: 700;">
                            {{ $minToHHMM($servico->tempo_deslocamento_min) }}
                            <span style="font-size: 14px; font-weight: 400;">
                                ({{ $servico->tempo_deslocamento_min ?? 0 }} min)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="alert alert-success mb-0">
                        <div class="small text-muted">Tempo de execução do serviço</div>
                        <div style="font-size: 22px; font-weight: 700;">
                            {{ $minToHHMM($servico->tempo_servico_min) }}
                            <span style="font-size: 14px; font-weight: 400;">
                                ({{ $servico->tempo_servico_min ?? 0 }} min)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MATERIAIS USADOS --}}
            <hr>
            <h5 class="mb-3"><b>Materiais utilizados</b></h5>

            @if($servico->materiais && $servico->materiais->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th style="width: 180px;">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servico->materiais as $material)
                                <tr>
                                    <td>{{ $material->equipamento }} - {{ $material->marca }}</td>
                                    <td>
                                        {{ $material->pivot->quantidade_usada }}
                                        {{ $material->unidade }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    Nenhum material foi lançado neste serviço.
                </div>
            @endif

        @endif

        {{-- BOTÕES --}}
        <div class="d-flex mt-3" style="gap: 10px; flex-wrap: wrap;">

            @if($servico->status === 'agendado')
                <form method="POST" action="{{ route('admin.servicos.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="aberto">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-folder-open"></i> Abrir serviço
                    </button>
                </form>
            @endif

            @if($servico->status === 'aberto')
                <form method="POST" action="{{ route('admin.servicos.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="em_execucao">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-play"></i> Iniciar execução
                    </button>
                </form>
            @endif

            @if($servico->status === 'em_execucao')
                <a href="{{ route('admin.servicos.materiais.create', $servico) }}" class="btn btn-primary">
                    <i class="fas fa-boxes"></i> Lançar / Editar materiais
                </a>

                <form method="POST" action="{{ route('admin.servicos.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="finalizado">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Finalizar serviço
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.servicos.index') }}" class="btn btn-outline-secondary">
                Voltar
            </a>
        </div>

    </div>
</div>
@endsection
