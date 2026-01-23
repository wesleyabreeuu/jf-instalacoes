@extends('layouts.adminlte')


@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">Editar Material</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.materiais.update', $material) }}">
        @csrf
        @method('PUT')
        @include('admin.materiais.form', ['material' => $material])
        <button class="btn btn-primary">Atualizar</button>
        <a href="{{ route('admin.materiais.index') }}" class="btn btn-secondary">Voltar</a>
      </form>
    </div>
  </div>
</div>
@endsection
