@php
  $equipamento = old('equipamento', $material->equipamento ?? '');
  $marca = old('marca', $material->marca ?? '');
  $quantidade = old('quantidade', $material->quantidade ?? 0);
  $unidade = old('unidade', $material->unidade ?? 'un');
  $ativo = old('ativo', $material->ativo ?? true);
@endphp

<div class="mb-3">
  <label class="form-label">Equipamento *</label>
  <input name="equipamento" class="form-control @error('equipamento') is-invalid @enderror" value="{{ $equipamento }}">
  @error('equipamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
  <label class="form-label">Marca</label>
  <input name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ $marca }}">
  @error('marca') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Quantidade *</label>
    <input type="number" min="0" name="quantidade" class="form-control @error('quantidade') is-invalid @enderror" value="{{ $quantidade }}">
    @error('quantidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Unidade *</label>
    <select name="unidade" class="form-select @error('unidade') is-invalid @enderror">
      @foreach(['un' => 'Unidade', 'm' => 'Metro', 'kg' => 'Quilo'] as $k => $v)
        <option value="{{ $k }}" @selected($unidade == $k)>{{ $v }}</option>
      @endforeach
    </select>
    @error('unidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4 mb-3 d-flex align-items-end">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo" @checked($ativo)>
      <label class="form-check-label" for="ativo">Ativo</label>
    </div>
  </div>
</div>
