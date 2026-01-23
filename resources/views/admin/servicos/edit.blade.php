@extends('layouts.adminlte')

@section('title', 'Editar Serviço')
@section('page-title', 'Editar Serviço')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Serviço</h3>
    </div>

    <form action="{{ route('admin.servicos.update', $servico) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
            @include('admin.servicos._form', ['servico' => $servico])
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('admin.servicos.index') }}" class="btn btn-secondary">Voltar</a>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </div>
    </form>
</div>
@endsection
