@php
  $editing = isset($user);
@endphp

<div class="card">
    <div class="card-body">

        <div class="form-group">
            <label>Nome</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $editing ? $user->name : '') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $editing ? $user->email : '') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Perfil</label>
            <select name="role" class="form-control" required>
                @php $roleOld = old('role', $editing ? $user->role : 'colaborador'); @endphp
                <option value="admin" {{ $roleOld === 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="colaborador" {{ $roleOld === 'colaborador' ? 'selected' : '' }}>Colaborador</option>
            </select>
            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <hr>

        <div class="form-group">
            <label>Senha {{ $editing ? '(deixe em branco para não alterar)' : '' }}</label>
            <input type="password" name="password" class="form-control" {{ $editing ? '' : 'required' }}>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Confirmar Senha</label>
            <input type="password" name="password_confirmation" class="form-control" {{ $editing ? '' : 'required' }}>
        </div>

    </div>
</div>
