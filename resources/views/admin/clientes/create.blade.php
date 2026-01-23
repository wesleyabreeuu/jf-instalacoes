@extends('layouts.adminlte')

@section('title', 'Novo Cliente - JF Instalações')
@section('page-title', 'Novo Cliente')

@section('content')
<div class="card" style="border-radius:16px;">
    <div class="card-header">
        <h3 class="card-title m-0" style="font-weight:800;">Cadastrar cliente</h3>
    </div>

    <form method="POST" action="{{ route('admin.clientes.store') }}">
        @csrf
        <div class="card-body">
            @include('admin.clientes.partials.form', ['cliente' => null])
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
