@extends('layouts.adminlte')

@section('title', 'Editar Usuário - JF Instalações')
@section('page-title', 'Editar Usuário')

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">Revise os campos destacados.</div>
    @endif

    <form action="{{ route('admin.usuarios.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.usuarios._form', ['user' => $user])
        <button class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
@endsection
