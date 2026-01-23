@extends('layouts.adminlte')

@section('title', 'Novo Serviço')
@section('page-title', 'Novo Serviço')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cadastro de Serviço</h3>
    </div>

    <form action="{{ route('admin.servicos.store') }}" method="POST">
        @csrf

        <div class="card-body">
            @include('admin.servicos._form')
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('admin.servicos.index') }}" class="btn btn-secondary">Voltar</a>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>
@endsection
