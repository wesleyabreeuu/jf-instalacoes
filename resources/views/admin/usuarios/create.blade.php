@extends('layouts.adminlte')

@section('title', 'Novo Usuário - JF Instalações')
@section('page-title', 'Novo Usuário')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cadastrar novo usuário</h3>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <b>Revise os campos:</b>
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>Perfil</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="colaborador" {{ old('role','colaborador') === 'colaborador' ? 'selected' : '' }}>Colaborador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Confirmar senha</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>

                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                    Voltar
                </a>
            </form>
        </div>
    </div>
@endsection
