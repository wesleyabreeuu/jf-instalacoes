@extends('layouts.adminlte')

@section('title', 'Clientes - JF Instalações')
@section('page-title', 'Clientes')

@section('content')
<div class="card" style="border-radius:16px;">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title m-0" style="font-weight:800;">Lista de clientes</h3>
        <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo cliente
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Cidade/UF</th>
                    <th class="text-right" style="width:200px;">Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nome }}</td>
                        <td>{{ $cliente->telefone ?? '-' }}</td>
                        <td>
                            {{ $cliente->cidade ?? '-' }}
                            @if($cliente->uf) / {{ $cliente->uf }} @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <form action="{{ route('admin.clientes.destroy', $cliente) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este cliente?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Nenhum cliente cadastrado.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-center">
        {{ $clientes->links() }}
    </div>
</div>
@endsection
