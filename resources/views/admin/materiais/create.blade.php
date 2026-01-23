@extends('layouts.adminlte')


@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-3">Novo Material</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.materiais.store') }}">
        @csrf
        @include('admin.materiais.form', ['material' => null])
        <button class="btn btn-primary">Salvar</button>
        <a href="{{ route('admin.materiais.index') }}" class="btn btn-secondary">Voltar</a>
      </form>
    </div>
  </div>
</div>
@endsection
