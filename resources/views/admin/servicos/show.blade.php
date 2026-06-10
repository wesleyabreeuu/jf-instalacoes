@extends('layouts.adminlte')

@section('title', 'Serviço')
@section('page-title', 'Serviço #'.$servico->id)

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@php
    $routeBase = $routeBase ?? 'admin.servicos';
    $isOrcamento = $servico->tipo_servico === 'orcamento';
    $isAdmin = auth()->user()?->isAdmin();
    $backRoute = $isOrcamento ? (auth()->user()?->isAdmin() ? 'admin.orcamentos.index' : 'app.orcamentos.index') : $routeBase.'.index';
    $statusColors = [
        'agendado' => 'info',
        'aberto' => 'secondary',
        'em_deslocamento' => 'primary',
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

            <p class="mb-1"><b>Tipo de serviço:</b> {{ $servico->tipo_servico_label }}</p>
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

        @if($isOrcamento)
            <h5 class="mb-3"><b>Dados do orçamento</b></h5>

            @if($servico->orcamento_descricao || $servico->orcamento_descricao_servico)
                <div class="row">
                    <div class="col-md-6">
                        <div class="small text-muted">Descrição do orçamento</div>
                        <div class="font-weight-bold" style="white-space: pre-line;">{{ $servico->orcamento_descricao ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Descrição do serviço previsto</div>
                        <div class="font-weight-bold" style="white-space: pre-line;">{{ $servico->orcamento_descricao_servico ?? '-' }}</div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="small text-muted">Tempo estimado de instalação</div>
                        <div class="font-weight-bold">{{ $servico->orcamento_tempo_instalacao_texto }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Data pré-agendada</div>
                        <div class="font-weight-bold">{{ $servico->orcamento_data_pre_agendada?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Orçamento finalizado em</div>
                        <div class="font-weight-bold">{{ $servico->orcamento_finalizado_em?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                </div>

                <hr>
            @else
                <div class="alert alert-info">
                    Orçamento ainda sem detalhamento preenchido.
                </div>
            @endif
        @endif

        {{-- SEÇÃO DETALHES (SÓ QUANDO FINALIZADO) --}}
        @if(!$isOrcamento && $servico->status === 'finalizado')
            <h5 class="mb-3"><b>Detalhes do serviço finalizado</b></h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="small text-muted">Início do deslocamento</div>
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

            @if($isOrcamento && in_array($servico->status, ['agendado', 'aberto'], true))
                <form method="POST" action="{{ route($routeBase.'.orcamento.iniciar', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-play"></i> Iniciar orçamento
                    </button>
                </form>
            @endif

            @if(!$isOrcamento && $servico->status === 'agendado')
                <form method="POST" action="{{ route($routeBase.'.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="aberto">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-folder-open"></i> Abrir serviço
                    </button>
                </form>
            @endif

            @if(!$isOrcamento && $servico->status === 'aberto')
                <form method="POST" action="{{ route($routeBase.'.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="em_deslocamento">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-route"></i> Iniciar deslocamento
                    </button>
                </form>
            @endif

            @if(!$isOrcamento && $servico->status === 'em_deslocamento')
                <form method="POST" action="{{ route($routeBase.'.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="em_execucao">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-play"></i> Iniciar execução
                    </button>
                </form>
            @endif

            @if(!$isOrcamento && $servico->status === 'em_execucao')
                <a href="{{ route($routeBase.'.materiais.create', $servico) }}" class="btn btn-primary">
                    <i class="fas fa-boxes"></i> Lançar / Editar materiais
                </a>

                <form method="POST" action="{{ route($routeBase.'.status', $servico) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="finalizado">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Finalizar serviço
                    </button>
                </form>
            @endif

            <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary">
                Voltar
            </a>
        </div>

    </div>
</div>

@if($isOrcamento && $servico->status === 'em_execucao')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Finalizar orçamento</h3>
        </div>
        <form method="POST" action="{{ route($routeBase.'.orcamento.finalizar', $servico) }}">
            @csrf
            @method('PATCH')
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Descrição do orçamento *</label>
                        <textarea name="orcamento_descricao" class="form-control" rows="5" required>{{ old('orcamento_descricao', $servico->orcamento_descricao) }}</textarea>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Descrição do serviço previsto *</label>
                        <textarea name="orcamento_descricao_servico" class="form-control" rows="5" required>{{ old('orcamento_descricao_servico', $servico->orcamento_descricao_servico) }}</textarea>
                        <small class="text-muted">Inclua materiais estimados, tempo de instalação e observações importantes.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tempo estimado de instalação (minutos) *</label>
                        <input type="number" name="orcamento_tempo_instalacao_min" class="form-control" min="1" value="{{ old('orcamento_tempo_instalacao_min', $servico->orcamento_tempo_instalacao_min) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Data pré-agendada *</label>
                        <input type="date" name="orcamento_data_pre_agendada" class="form-control" value="{{ old('orcamento_data_pre_agendada', $servico->orcamento_data_pre_agendada?->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Finalizar orçamento
                </button>
            </div>
        </form>
    </div>
@endif

@if($isOrcamento && $servico->status === 'finalizado' && $isAdmin)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Converter orçamento em serviço</h3>
        </div>
        <form method="POST" action="{{ route($routeBase.'.orcamento.converter', $servico) }}">
            @csrf
            @method('PATCH')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Virar *</label>
                        <select name="tipo_servico" class="form-control" required>
                            <option value="instalacao" @selected(old('tipo_servico') === 'instalacao')>Instalação</option>
                            <option value="manutencao" @selected(old('tipo_servico') === 'manutencao')>Manutenção</option>
                        </select>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Data correta *</label>
                        <input type="date" name="data" class="form-control" value="{{ old('data', $servico->orcamento_data_pre_agendada?->format('Y-m-d') ?? $servico->data?->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Horário previsto</label>
                        <input type="time" name="hora_prevista" class="form-control" value="{{ old('hora_prevista', $servico->hora_prevista) }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Colaborador *</label>
                        <select name="colaborador_id" class="form-control" required>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected((string)old('colaborador_id', $servico->colaborador_id) === (string)$usuario->id)>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-exchange-alt"></i> Converter e seguir fluxo normal
                </button>
            </div>
        </form>
    </div>
@endif
@endsection
