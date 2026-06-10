@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $statusList = [
        'agendado' => 'AGENDADO',
        'aberto' => 'ABERTO',
        'em_deslocamento' => 'EM DESLOCAMENTO',
        'em_execucao' => 'EM EXECUÇÃO',
        'finalizado' => 'FINALIZADO',
        'cancelado' => 'CANCELADO',
    ];

    $tipoServicoList = [
        'instalacao' => 'INSTALAÇÃO',
        'manutencao' => 'MANUTENÇÃO',
        'orcamento' => 'ORÇAMENTO',
    ];
@endphp

<div class="row">
    <div class="col-md-6 form-group">
        <label>Cliente *</label>

        <select name="cliente_id" id="cliente_id" class="form-control" required>
            <option value="">Selecione</option>

            @foreach($clientes as $cliente)
                @php
                    $endereco = trim(
                        ($cliente->rua ?? '') . ', ' .
                        ($cliente->numero ?? '') . ' - ' .
                        ($cliente->bairro ?? '') . ', ' .
                        ($cliente->cidade ?? '') . '/' .
                        ($cliente->uf ?? '')
                    );
                @endphp

                <option
                    value="{{ $cliente->id }}"
                    data-endereco="{{ $endereco }}"
                    {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}
                >
                    {{ $cliente->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 form-group">
        <label>Colaborador *</label>
        <select name="colaborador_id" class="form-control" required>
            <option value="">Selecione</option>
            @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}"
                    {{ old('colaborador_id') == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-8 form-group">
        <label>Local de instalação</label>
        <input
            type="text"
            name="local_instalacao"
            id="local_instalacao"
            class="form-control"
            value="{{ old('local_instalacao') }}"
            placeholder="Será preenchido pelo endereço do cliente (pode editar)"
        >
        <small class="text-muted">
            Se a instalação for em outro local, você pode alterar aqui.
        </small>
    </div>

    <div class="col-md-4 form-group">
        <label>Tipo de serviço *</label>
        <select name="tipo_servico" class="form-control" required>
            <option value="">Selecione</option>
            @foreach($tipoServicoList as $value => $label)
                <option value="{{ $value }}" {{ old('tipo_servico', request('tipo_servico')) == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Data *</label>
        <input type="date" name="data" class="form-control" value="{{ old('data') }}" required>
    </div>

    <div class="col-md-4 form-group">
        <label>Horário previsto</label>
        <input type="time" name="hora_prevista" class="form-control" value="{{ old('hora_prevista') }}">
    </div>

    <div class="col-md-4 form-group">
        <label>Status *</label>
        <select name="status" class="form-control" required>
            @foreach($statusList as $value => $label)
                <option value="{{ $value }}"
                    {{ old('status', 'agendado') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<script>
(function () {
    const clienteSelect = document.getElementById('cliente_id');
    const localInput = document.getElementById('local_instalacao');

    if (!clienteSelect || !localInput) return;

    function preencherLocalSeVazio() {
        const opt = clienteSelect.options[clienteSelect.selectedIndex];
        const endereco = opt ? (opt.getAttribute('data-endereco') || '') : '';

        // só autopreenche se estiver vazio (não sobrescreve edição manual)
        if (!localInput.value.trim()) {
            localInput.value = endereco;
        }
    }

    clienteSelect.addEventListener('change', preencherLocalSeVazio);

    // ao carregar, preenche se estiver vazio (bom para primeira vez / quando volta com erro)
    window.addEventListener('load', preencherLocalSeVazio);
})();
</script>
