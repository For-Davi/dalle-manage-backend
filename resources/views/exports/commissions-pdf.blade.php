<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Comissões</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #2d3748;
            margin: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #1a202c;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #718096;
        }

        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px;
        }

        .label {
            font-weight: bold;
            color: #4a5568;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.data-table thead {
            background: #2b6cb0;
            color: #ffffff;
        }

        table.data-table th {
            padding: 10px;
            font-size: 11px;
            text-align: left;
        }

        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f7fafc;
        }

        .text-right {
            text-align: right;
        }

        .total-box {
            margin-top: 20px;
            padding: 12px;
            background: #edf2f7;
            border: 1px solid #cbd5e0;
            border-radius: 5px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Relatório de Comissões</h2>
        <p>Gerado em: {{ now()->timezone('America/Sao_Paulo')->format('d/m/Y H:i:s') }}</p>
    </div>

    @if($commissionDetails->count() > 0)

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>
                    <span class="label">Vendedor:</span>
                    {{ $commissionDetails->first()->seller_name }}
                </td>
                <td>
                    <span class="label">Período:</span>
                    {{ request('period') }}
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">E-mail:</span>
                    {{ $commissionDetails->first()->seller_email ?? 'N/A' }}
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID Venda</th>
                <th>Data</th>
                <th>Cliente</th>
                <th class="text-right">Total (R$)</th>
                <th class="text-right">Comissão (R$)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalComissao = 0; @endphp

            @foreach($commissionDetails as $detail)
                @php $totalComissao += $detail->commission_value; @endphp
                <tr>
                    <td>{{ $detail->sale_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($detail->sale_date)->format('d/m/Y') }}</td>
                    <td>{{ $detail->client_name }}</td>
                    <td class="text-right">{{ number_format($detail->sale_total, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->commission_value, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Total de Comissão: R$ {{ number_format($totalComissao, 2, ',', '.') }}
    </div>

    @else
        <p style="text-align:center; margin-top:40px;">
            Nenhuma comissão encontrada para este período.
        </p>
    @endif

    <div class="footer">
        Relatório de Comissões • Sistema de Gestão
    </div>

</body>
</html>
