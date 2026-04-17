<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cupom Fiscal</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      font-size: 12px;
      margin: 0;
      padding: 8px;
      color: #000;
    }

    .coupon-container {
      border: 1px solid #000;
      padding: 12px;
      margin: 0 auto;
      max-width: 100%;
    }

    .header-section {
      margin-bottom: 8px;
    }

    .flex {
      display: flex;
    }

    .column {
      flex-direction: column;
    }

    .row {
      flex-direction: row;
    }

    .justify-between {
      justify-content: space-between;
    }

    .align-center {
      align-items: center;
    }

    .full-width {
      width: 100%;
    }

    .text-bold {
      font-weight: bold;
    }

    .text-h6 {
      font-size: 14px;
    }

    .text-h5 {
      font-size: 16px;
    }

    .text-body1 {
      font-size: 12px;
    }

    .text-body2 {
      font-size: 10px;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .separator {
      border-top: 1px dashed #000;
      margin: 8px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }

    th, td {
      padding: 4px;
      border: none;
    }

    th {
      font-weight: bold;
    }

    .product-row td {
      max-width: 150px;
      word-wrap: break-word;
    }
  </style>
</head>
<body>
  <div class="coupon-container">
    <div class="header-section">

      <!-- Nome e Data -->
      <div class="flex row justify-between align-center full-width">
        <span class="text-bold text-h6">{{ isset($couponData['enterprise']['name']) ? strtoupper($couponData['enterprise']['name']) : '' }}</span>
        <span class="text-bold text-h6">
          {{ \Carbon\Carbon::parse($couponData['created_at'])->format('d/m/Y H:i:s') }}
        </span>
      </div>

      @if(isset($couponData['enterprise']['cnpj']) || isset($couponData['enterprise']['cpf']))
        <div>
          <span class="text-bold text-h6">
            {{ isset($couponData['enterprise']['cnpj']) ? 'CNPJ: ' . $couponData['enterprise']['cnpj'] : 'CPF: ' . ($couponData['enterprise']['cpf'] ?? '') }}
          </span>
        </div>
      @endif

      <div class="separator"></div>

      <div class="text-center text-bold text-h6">Cupom Fiscal</div>

      <table>
        <thead>
          <tr>
            <th class="text-left text-body2 text-bold">ITEM</th>
            <th class="text-left text-body2 text-bold">CÓDIGO</th>
            <th class="text-left text-body2 text-bold">DESCRIÇÃO</th>
            <th class="text-left text-body2 text-bold">QTD</th>
            <th class="text-right text-body2 text-bold">VALOR</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($couponData['returnExchangeItems']) && count($couponData['returnExchangeItems']) > 0)
            @foreach($couponData['returnExchangeItems'] as $index => $product)
              <tr class="product-row">
                <td class="text-left">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                <td class="text-left">{{ $product['product_sku'] ?? '' }}</td>
                <td class="text-left">{{ $product['product_name'] ?? '' }}</td>
                <td class="text-left">{{ $product['quantity'] ?? '' }}</td>
                <td class="text-right">R$ {{ $product['product_price'] ?? '' }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">Nenhum produto para mostrar</td>
            </tr>
          @endif
        </tbody>
      </table>

      <div class="separator"></div>

      <div class="flex row justify-between full-width">
        <div>
          <div class="text-bold text-body1">TROCO: R$ {{ $couponData['freight_change'] > 0 ? $couponData['freight_change'] : $couponData['change'] }}</div>
          <div class="text-right text-bold text-h6">TOTAL: R$ {{ $couponData['difference_value'] > 0 ? $couponData['difference_value'] : $couponData['delivery']['freight_value'] }}</div>
        </div>
        <div class="text-bold text-body1">
          TARIFAS: R$ {{ $couponData['freight_fees'] > 0 ? $couponData['freight_fees'] : $couponData['fees'] }}
        </div>
        <div class="text-bold text-body1" style="margin-top: 6px;">
            FRETE: R$ {{ $couponData['delivery']['freight_value']  }}
          </div>
      </div>
    </div>
  </div>
</body>
</html>
