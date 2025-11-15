<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedido #{{ $order->order_number }}</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        :root {
            --primary-color: #0D47A1;
            --primary-light: #e3f2fd;
            --border-color: #ddd;
        }

        .header {
            background: #0D47A1;
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .section {
            margin-bottom: 20px;
            padding: 0 5px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0D47A1;
            margin-bottom: 10px;
            border-bottom: 2px solid #0D47A1;
            padding-bottom: 3px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table th, .info-table td {
            border: 1px solid var(--border-color);
            padding: 8px;
            text-align: left;
        }

        .info-table th {
            background: var(--primary-light);
            color: #333;
            font-weight: bold;
            font-size: 10px;
            width: 25%;
        }

        .info-table td {
            font-size: 10px;
        }

        .status-cell {
            font-weight: bold;
        }

        .item-card {
            border: 2px solid var(--primary-color);
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 25px;
            background-color: var(--primary-light);
            page-break-inside: avoid;
        }

        .item-header {
            background: #0D47A1;
            color: white;
            padding: 5px 10px;
            margin: -10px -10px 10px -10px;
            font-size: 11px;
            font-weight: bold;
            border-top-left-radius: 2px;
            border-top-right-radius: 2px;
        }

        .item-detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .item-detail-table td {
            border: none;
            padding: 2px 0;
            font-size: 10px;
            width: 25%;
        }

        .item-detail-table .label {
            font-weight: bold;
            color: #555;
            padding-right: 5px;
        }

        .color-box {
            width: 12px;
            height: 12px;
            border: 1px solid #222;
            display: inline-block;
            vertical-align: middle;
            margin-left: 3px;
        }

        .text-strong { font-weight: bold; }
        .text-center { text-align: center; }
        .small-info { font-size: 9px; color: #777; }

    </style>
</head>
<body>

@php
    function safeDateFormat($date, $format = 'd/m/Y') {
        if (!$date) return '-';
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) return $date;
        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }

    // Nova função igual ao getLabelStatus, mas sem cor
    function getStatusLabel($value) {
        return match ($value) {
            'canceled'            => 'Cancelado',
            'completely_finished' => 'Completo total',
            'partial_finished'    => 'Completo parcial',
            'waiting'             => 'Aguardando',
            'conference'          => 'Conferência',
            default               => 'Indefinido',
        };
    }

    $statusText = getStatusLabel($order->status ?? null);
@endphp


<div class="header">
    <h1>PEDIDO DE COMPRA #{{ $order->order_number ?? 'N/A' }}</h1>
</div>

<div class="section">
    <div class="section-title">DETALHES GERAIS</div>

    <table class="info-table">
        <thead>
            <tr>
                <th>NÚMERO DO PEDIDO</th>
                <th>STATUS</th>
                <th>TOTAL DO PEDIDO</th>
                <th>DATA DE EMISSÃO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-strong">{{ $order->order_number ?? 'N/A' }}</td>

                {{-- status sem cor --}}
                <td class="status-cell">{{ $statusText }}</td>

                <td class="text-strong highlight">
                    R$ {{ number_format($order->items->sum('total_cost'), 2, ',', '.') }}
                </td>

                <td>{{ safeDateFormat($order->date_issue) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="info-table">
        <thead>
            <tr>
                <th>CRIADO POR</th>
                <th>PREVISÃO DE ENTREGA</th>
                <th>CRIADO EM</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $order->user->name ?? 'N/A' }}
                    <span class="small-info">({{ $order->user->email ?? 'N/A' }})</span>
                </td>
                <td>{{ safeDateFormat($order->date_delivery_expected) }}</td>
                <td>{{ safeDateFormat($order->created_at, 'd/m/Y H:i') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="info-table">
        <thead>
            <tr>
                <th>OBSERVAÇÕES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->observation ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">ITENS REQUISITADOS ({{ $order->items->count() ?? 0 }})</div>

    @forelse ($order->items as $item)
        <div class="item-card">
            <div class="item-header">
                {{ $item->variant->product->name ?? 'Produto Desconhecido' }} &bull;
                SKU: {{ $item->variant->sku ?? 'N/A' }}
            </div>

            <table class="item-detail-table">
                <tr>
                    <td><span class="label">Código:</span> {{ $item->variant->code ?? '-' }}</td>
                    <td><span class="label">Grade:</span> {{ $item->variant->gridItem->size ?? '-' }}</td>
                    <td><span class="label">Custo Unitário:</span> R$
                        {{ number_format($item->unit_cost, 2, ',', '.') }}
                    </td>
                    <td><span class="label">TOTAL ITEM:</span>
                        <span class="text-strong highlight">R$
                            {{ number_format($item->total_cost, 2, ',', '.') }}
                        </span>
                    </td>
                </tr>
            </table>

            <hr style="border: none; border-top: 1px dashed #ccc; margin: 8px 0;">

            <table class="item-detail-table">
                <tr>
                    <td>
                        <span class="label">Cor:</span> {{ $item->variant->color->name ?? '-' }}
                        @if ($item->variant->color?->hex_color_code)
                            <span class="color-box"
                                  style="background: {{ $item->variant->color->hex_color_code }}"></span>
                        @endif
                    </td>

                    <td><span class="label">Qtde Requisitada:</span>
                        <span class="text-strong">{{ $item->quantity_requested }}</span>
                    </td>

                    <td><span class="label">Qtde Recebida:</span> {{ $item->quantity_received ?? '-' }}</td>

                    <td><span class="label">Finalizado:</span> {{ $item->finished ? 'Sim' : 'Não' }}</td>
                </tr>
            </table>

            <table class="item-detail-table">
                <tr>
                    <td><span class="label">Data de Recebimento:</span>
                        {{ safeDateFormat($item->date_received) }}
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
    @empty
        <p>Não há itens registrados neste pedido.</p>
    @endforelse
</div>

</body>
</html>
