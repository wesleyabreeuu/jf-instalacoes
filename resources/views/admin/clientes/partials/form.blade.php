@php
    $v = fn($field) => old($field, $cliente?->$field);
@endphp

<div class="form-group">
    <label>Nome *</label>
    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
           value="{{ $v('nome') }}" required>
    @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Telefone</label>
    <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror"
           value="{{ $v('telefone') }}">
    @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-8">
        <label>Rua</label>
        <input type="text" name="rua" class="form-control @error('rua') is-invalid @enderror"
               value="{{ $v('rua') }}">
        @error('rua') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group col-md-4">
        <label>Número</label>
        <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror"
               value="{{ $v('numero') }}">
        @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Bairro</label>
        <input type="text" name="bairro" class="form-control @error('bairro') is-invalid @enderror"
               value="{{ $v('bairro') }}">
        @error('bairro') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group col-md-4">
        <label>Cidade</label>
        <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror"
               value="{{ $v('cidade') }}">
        @error('cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group col-md-2">
        <label>UF</label>
        <input type="text" name="uf" maxlength="2"
               class="form-control text-uppercase @error('uf') is-invalid @enderror"
               value="{{ $v('uf') }}">
        @error('uf') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Ex: RJ</small>
    </div>
</div>
