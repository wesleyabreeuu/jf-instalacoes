<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        .header { border-bottom: 2px solid #071827; padding-bottom: 10px; margin-bottom: 14px; }
        .row { display: table; width: 100%; }
        .col-left { display: table-cell; vertical-align: middle; width: 70%; }
        .col-right { display: table-cell; vertical-align: middle; width: 30%; text-align: right; }

        .logo { width: 48px; height: 48px; border-radius: 50%; border: 2px solid #071827; }
        .title { font-size: 18px; font-weight: 800; margin: 0; }
        .sub { color:#444; margin: 2px 0 0 0; }

        .badge { display:inline-block; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 11px; background: #F4C21A; color: #071827; }

        .box { border: 1px solid #ddd; border-radius: 8px; padding: 10px; margin-top: 10px; }
        .label { color:#555; font-size: 11px; }
        .value { font-weight: 700; margin-top: 2px; }

        .grid { width:100%; border-collapse: collapse; margin-top: 10px;}
        .grid th, .grid td { border: 1px solid #ddd; padding: 8px; }
        .grid th { background:#f5f5f5; text-align:left; }
    </style>
</head>
<body>

    <div class="header">
        <div class="row">
            <div class="col-left">
                <p class="title">JF Instalações</p>
                <p class="sub">Relatório do Serviço #{{ $servico->id }}</p>
                <span class="badge">{{ strtoupper(str_replace('_',' ', $servico->status ?? '')) }}</span>
                <span class="badge">{{ strtoupper($servico->tipo_servico_label) }}</span>
            </div>
            <div class="col-right">
                @if($logoBase64)
                    <img class="logo" src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </div>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col-left">
                <div class="label">Cliente</div>
                <div class="value">{{ $servico->cliente->nome ?? '-' }}</div>
            </div>
            <div class="col-right" style="text-align:left;">
                <div class="label">Cadastro do serviço</div>
                <div class="value">
                    {{ optional($servico->created_at)->format('d/m/Y H:i') ?? '-' }}
                </div>
            </div>
        </div>

        <div class="label">Endereço</div>
        <div class="value">
            @php
                $c = $servico->cliente;
                $endereco = collect([
                    $c->rua ?? null,
                    $c->numero ? 'Nº '.$c->numero : null,
                    $c->bairro ?? null,
                    $c->cidade ?? null,
                    $c->uf ?? null,
                ])->filter()->implode(' - ');
            @endphp

            {{ $endereco ?: '-' }}
        </div>


    <table class="grid">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Data do serviço</th>
                <th>Hora prevista</th>
                <th>Colaborador</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $servico->tipo_servico_label }}</td>
                <td>{{ !empty($servico->data) ? \Carbon\Carbon::parse($servico->data)->format('d/m/Y') : '-' }}</td>
                <td>{{ $servico->hora_prevista ?? '-' }}</td>
                <td>{{ $servico->colaborador->name ?? $servico->user->name ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
