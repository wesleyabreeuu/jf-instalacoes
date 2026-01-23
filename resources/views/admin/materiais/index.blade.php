@extends('layouts.adminlte')
@section('title', 'Materiais - JF Instalações')
@section('page-title', 'Materiais')


@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Lista de Materiais</h1>
    <a href="{{ route('admin.materiais.create') }}" class="btn btn-primary">Novo Material</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Equipamento</th>
            <th>Marca</th>
            <th>Quantidade</th>
            <th>Unidade</th>
            <th>Ativo</th>
            <th width="160">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($materiais as $m)
            <tr>
              <td>{{ $m->equipamento }}</td>
              <td>{{ $m->marca ?? '-' }}</td>
              <td>{{ $m->quantidade }}</td>
              <td>{{ $m->unidade }}</td>
              <td>
                @if($m->ativo)
                  <span class="badge bg-success">Sim</span>
                @else
                  <span class="badge bg-secondary">Não</span>
                @endif
              </td>
              <td>
                <a class="btn btn-sm btn-warning" href="{{ route('admin.materiais.edit', $m) }}">Editar</a>

                <form action="{{ route('admin.materiais.destroy', $m) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Excluir este material?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center">Nenhum material cadastrado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{ $materiais->links() }}
    </div>
  </div>
</div>
@endsection
