@extends('layouts.adminlte')

@section('title', 'Materiais do Serviço')
@section('page-title', 'Materiais - Serviço #'.$servico->id)

@section('content')

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Lançar materiais usados</h3>
  </div>

  <form method="POST" action="{{ route('admin.servicos.materiais.store', $servico) }}">
    @csrf

    <div class="card-body">

      @if($lancados->count())
        <div class="alert alert-info">
          <b>Já lançados:</b>
          <ul class="mb-0">
            @foreach($lancados as $m)
              <li>
                {{ $m->equipamento }} - {{ $m->marca }}:
                {{ $m->pivot->quantidade_usada }} {{ $m->unidade }}
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <div id="itens">
        <div class="row mb-2 item">
          <div class="col-md-7">
            <label class="mb-1">Material</label>
            <select name="itens[0][material_id]" class="form-control" required>
              <option value="">Selecione...</option>
              @foreach($materiais as $material)
                <option value="{{ $material->id }}">
                  {{ $material->equipamento }} - {{ $material->marca }}
                  ({{ $material->unidade }}) | Estoque: {{ $material->quantidade }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="mb-1">Qtd usada</label>
            <input type="number" step="0.01" min="0.01"
                   name="itens[0][quantidade_usada]"
                   class="form-control" required>
          </div>

          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger w-100 remover">
              Remover
            </button>
          </div>
        </div>
      </div>

      <button type="button" id="add" class="btn btn-secondary mt-2">
        + Adicionar material
      </button>

    </div>

    <div class="card-footer text-right">
      <a href="{{ route('admin.servicos.show', $servico) }}" class="btn btn-secondary">
        Voltar
      </a>
      <button type="submit" class="btn btn-primary">
        Salvar materiais
      </button>
    </div>
  </form>
</div>

<script>
  let index = 1;

  document.getElementById('add').addEventListener('click', function () {
    const container = document.getElementById('itens');
    const first = container.querySelector('.item');
    const clone = first.cloneNode(true);

    clone.querySelectorAll('select, input').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
      el.value = '';
    });

    container.appendChild(clone);
    index++;
  });

  document.addEventListener('click', function(e){
    if(e.target.classList.contains('remover')){
      const items = document.querySelectorAll('#itens .item');
      if(items.length > 1){
        e.target.closest('.item').remove();
      }
    }
  });
</script>

@endsection
