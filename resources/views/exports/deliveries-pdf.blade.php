<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Entregas</title>

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

        .delivery-block {
            margin-bottom: 70px;
        }

        .delivery-title {
            background: #2b6cb0;
            color: #ffffff;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
            display: flex;
            justify-content: space-between;
            page-break-after: avoid;
        }

        .info-section {
            padding: 10px;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-top: none;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 6px;
            font-size: 11px;
        }

        .label {
            font-weight: bold;
            color: #4a5568;
        }

        .section-label {
            font-size: 11px;
            font-weight: bold;
            color: #2b6cb0;
            margin: 14px 0 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.data-table thead {
            background: #2b6cb0;
            color: #ffffff;
            display: table-row-group;
        }

        table.data-table th {
            padding: 8px 10px;
            font-size: 10px;
            text-align: left;
        }

        table.data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f7fafc;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge {
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .badge-sale      { background: #bee3f8; color: #1a365d; }
        .badge-return    { background: #fed7d7; color: #742a2a; }
        .badge-pendent          { background: #fefcbf; color: #744210; }
        .badge-scheduled        { background: #bee3f8; color: #1a365d; }
        .badge-delivered        { background: #c6f6d5; color: #1c4532; }
        .badge-partial-delivered { background: #e9d8fd; color: #44337a; }
        .badge-delivered-in-person { background: #c6f6d5; color: #1c4532; }

        .observation-box {
            margin-top: 10px;
            padding: 7px 10px;
            background: #fffbeb;
            border-left: 3px solid #f6ad55;
            font-size: 11px;
            color: #744210;
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

        .page-break { margin-bottom: 30px; }
    </style>
</head>

<body>

    <div class="header">
        <h2>Relatório de Entregas</h2>
        <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @php
        $statusMap = [
            'pendent'             => ['label' => 'Pendente',                'badge' => 'badge-pendent'],
            'scheduled'           => ['label' => 'Agendado',                'badge' => 'badge-scheduled'],
            'delivered'           => ['label' => 'Entregue',                'badge' => 'badge-delivered'],
            'partial_delivered'   => ['label' => 'Entregue parcialmente',   'badge' => 'badge-partial-delivered'],
            'delivered_in_person' => ['label' => 'Entregue pessoalmente',   'badge' => 'badge-delivered-in-person'],
        ];
    @endphp

    @forelse ($deliveries as $index => $delivery)
        @php
            $isReturn   = (bool) $delivery->return_id;
            $items      = $isReturn ? $delivery->returnExchangeItems : $delivery->saleItems;
            $statusInfo = $statusMap[$delivery->status] ?? ['label' => $delivery->status, 'badge' => 'badge-pendent'];
        @endphp

        <div class="delivery-block">

            <div class="delivery-title">
                <span>Entrega #{{ $index + 1 }} — {{ $delivery->recipient_name }}</span>
                <span>
                    <span class="badge {{ $isReturn ? 'badge-return' : 'badge-sale' }}">
                        {{ $isReturn ? 'Devolução / Troca' : 'Venda' }}
                    </span>
                    &nbsp;
                    <span class="badge {{ $statusInfo['badge'] }}">
                        {{ $statusInfo['label'] }}
                    </span>
                </span>
            </div>

            <div class="info-section">

                <p class="section-label">Dados da entrega</p>

                <table class="info-table">
                    <tr>
                        <td><span class="label">Destinatário:</span> {{ $delivery->recipient_name ?? '—' }}</td>
                        <td><span class="label">Telefone:</span> {{ $delivery->recipient_phone ?? '—' }}</td>
                        <td><span class="label">Frete:</span> R$ {{ number_format($delivery->freight_value, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="label">CEP:</span> {{ $delivery->cep ?? '—' }}</td>
                        <td><span class="label">Estado:</span> {{ $delivery->state ?? '—' }}</td>
                        <td><span class="label">Cidade:</span> {{ $delivery->city ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td><span class="label">Bairro:</span> {{ $delivery->neighborhood ?? '—' }}</td>
                        <td><span class="label">Endereço:</span> {{ $delivery->address }}, {{ $delivery->number_address }}</td>
                        <td><span class="label">Complemento:</span> {{ $delivery->complement ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td><span class="label">Entregador:</span> {{ $delivery->delivery_guy_name ?? '—' }}</td>
                        <td><span class="label">Tel. entregador:</span> {{ $delivery->delivery_guy_phone ?? '—' }}</td>
                        <td><span class="label">Data agendada:</span> {{ $delivery->scheduled_date ? \Carbon\Carbon::parse($delivery->scheduled_date)->format('d/m/Y') : '—' }}</td>
                    </tr>
                </table>

                @if ($delivery->observation)
                    <div class="observation-box">
                        <span class="label">Observação:</span> {{ $delivery->observation }}
                    </div>
                @endif

                <p class="section-label">
                    Produtos — {{ $isReturn ? 'Itens de devolução / troca' : 'Itens de venda' }}
                </p>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th>Código</th>
                            <th>Cor</th>
                            <th>Grade</th>
                            <th class="text-center">Qtd.</th>
                            @if (!$isReturn)
                                <th class="text-center">Entregue</th>
                                <th class="text-center">Qtd. entregue</th>
                            @endif
                            <th class="text-right">Preço unit.</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->product_sku ?? '—' }}</td>
                                <td>{{ $item->product_code ?? '—' }}</td>
                                <td>{{ $item->product_color_name ?? '—' }}{{ $item->product_color ? ' (' . $item->product_color . ')' : '' }}</td>
                                <td>{{ $item->product_grid_name ?? $item->product_grid_size ?? '—' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                @if (!$isReturn)
                                    <td class="text-center">{{ $item->delivered ? 'Sim' : 'Não' }}</td>
                                    <td class="text-center">{{ $item->quantity_delivered ?? '—' }}</td>
                                @endif
                                <td class="text-right">R$ {{ number_format($item->product_price, 2, ',', '.') }}</td>
                                <td class="text-right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center" style="padding: 12px; color: #718096;">
                                    Nenhum produto encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    @empty
        <p style="text-align:center; margin-top:40px;">
            Nenhuma entrega encontrada para os filtros selecionados.
        </p>
    @endforelse

    <div class="footer">
        Relatório de Entregas • Sistema de Gestão &nbsp;|&nbsp; Total: {{ count($deliveries) }} entrega(s)
    </div>

</body>
</html>