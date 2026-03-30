@extends('layouts.adminlte')

@section('title', 'Serviços')
@section('page-title', 'Serviços')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@php
    $routeBase = $routeBase ?? 'admin.servicos';
    $isAdmin = auth()->user()?->isAdmin();
    $statusColors = [
        'agendado' => 'info',
        'aberto' => 'secondary',
        'em_execucao' => 'warning',
        'finalizado' => 'success',
        'cancelado' => 'danger',
    ];
@endphp

{{-- ✅ FILTROS --}}
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route($routeBase.'.index') }}">
            <div class="row">

                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        @foreach($statusList as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($isAdmin)
                    <div class="col-md-3">
                        <label>Colaborador</label>
                        <select name="colaborador_id" class="form-control">
                            <option value="">Todos</option>
                            @foreach($colaboradores as $c)
                                <option value="{{ $c->id }}" @selected((string)request('colaborador_id') === (string)$c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-3">
                    <label>Data (de)</label>
                    <input type="date" name="data_de" class="form-control" value="{{ request('data_de') }}">
                </div>

                <div class="col-md-3">
                    <label>Data (até)</label>
                    <input type="date" name="data_ate" class="form-control" value="{{ request('data_ate') }}">
                </div>

            </div>

            <div class="mt-3 d-flex" style="gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>

                <a href="{{ route($routeBase.'.index') }}" class="btn btn-outline-secondary">
                    Limpar
                </a>

                {{-- ✅ Novo Serviço só para admin --}}
                @if($isAdmin)
                    <a href="{{ route($routeBase.'.create') }}" class="btn btn-primary ml-auto">
                        <i class="fas fa-plus"></i> Novo Serviço
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ✅ TABELA --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Serviços</h3>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Colaborador</th>
                    <th>Data</th>
                    <th>Hora Prevista</th>
                    <th>Status</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servicos as $servico)
                    <tr>
                        <td>{{ $servico->id }}</td>
                        <td>{{ $servico->cliente->nome ?? '-' }}</td>
                        <td>{{ $servico->colaborador->name ?? '-' }}</td>
                        <td>{{ $servico->data?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $servico->hora_prevista ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $statusColors[$servico->status] ?? 'secondary' }}">
                                {{ strtoupper(str_replace('_',' ', $servico->status)) }}
                            </span>
                        </td>

                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route($routeBase.'.show', $servico) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Ver
                            </a>

                            {{-- ✅ PDF --}}
                            <a href="{{ route($routeBase.'.pdf', $servico) }}"
                               class="btn btn-sm btn-danger"
                               title="Gerar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>

                            {{-- ✅ Excluir só para admin --}}
                            @if($isAdmin)
                                <form action="{{ route($routeBase.'.destroy', $servico) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Deseja realmente excluir este serviço?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Nenhum serviço cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $servicos->links() }}
    </div>
</div>
@endsection
