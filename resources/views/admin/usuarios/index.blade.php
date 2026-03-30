@extends('layouts.adminlte')

@section('title', 'Usuários - JF Instalações')
@section('page-title', 'Usuários')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title m-0">Lista de usuários</h3>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo usuário
            </a>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th class="text-right" style="width: 200px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge badge-{{ $u->isAdmin() ? 'danger' : 'info' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.usuarios.edit', $u) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <form action="{{ route('admin.usuarios.destroy', $u) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" {{ auth()->id() === $u->id ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center p-4">Nenhum usuário cadastrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
@endsection
