<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { margin: 0 0 10px 0; }
        .meta { margin-bottom: 10px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; text-align: left; }
        .right { text-align: right; }
        .cards { margin: 10px 0 12px 0; }
        .card { display:inline-block; padding:8px 10px; border:1px solid #ddd; margin-right:8px; border-radius:6px; }
    </style>
</head>
<body>
    <h2>Relatório de Serviços — JF Instalações</h2>

    <div class="meta">
        Período: {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($fim)->format('d/m/Y') }}
        <br>
        Status: {{ $status === 'todos' ? 'Todos' : strtoupper(str_replace('_',' ',$status)) }}
        <br>
        Tipo: {{ $tipoServico === 'todos' ? 'Todos' : ($tipoServico === 'instalacao' ? 'Instalação' : 'Manutenção') }}
    </div>

    <div class="cards">
        <span class="card">Total: <b>{{ $totalRegistros }}</b></span>
        <span class="card">Finalizados: <b>{{ $totalFinalizado }}</b></span>
        <span class="card">Em execução: <b>{{ $totalExecucao }}</b></span>
        <span class="card">Cancelados: <b>{{ $totalCancelado }}</b></span>
        <span class="card">Valor: <b>R$ {{ number_format($totalValor ?? 0, 2, ',', '.') }}</b></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Local</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($servicos as $s)
            <tr>
                <td>{{ \Carbon\Carbon::parse($s->data)->format('d/m/Y') }}</td>
                <td>{{ $s->cliente->nome ?? '-' }}</td>
                <td>{{ $s->tipo_servico_label }}</td>
                <td>{{ $s->local_instalacao ?? '-' }}</td>
                <td>{{ strtoupper(str_replace('_',' ',$s->status)) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
