<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cupom Fiscal</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f9f9f9;
      color: #333;
      margin: 0;
      padding: 20px;
    }

    .card {
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 6px;
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 8px;
    }

    .enterprise-name {
      font-size: 18px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .sale-date {
      font-size: 16px;
      font-weight: bold;
    }

    .divider {
      width: 100%;
      border-top: 1px dashed #aaa;
      margin: 12px 0;
      height: 1px;
    }

    .intro {
      background-color: #f3f3f3;
      border-left: 4px solid #999;
      padding: 10px 12px;
      border-radius: 4px;
      font-size: 14px;
      line-height: 1.5;
      margin-bottom: 18px;
      color: #444;
    }

    .intro strong {
      color: #000;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 6px 4px;
      font-size: 13px;
      text-align: left;
      vertical-align: top;
    }

    th {
      font-weight: bold;
      border-bottom: 1px solid #ccc;
    }

    td.text-right {
      text-align: right;
    }

    .totals {
      margin-top: 10px;
    }

    .totals div {
      display: flex;
      justify-content: space-between;
      margin-top: 4px;
      font-size: 14px;
    }

    .totals .total {
      font-weight: bold;
      font-size: 16px;
    }
  </style>
</head>
<body>
    <h1>{{ $coupon['enterprise']->name }}</h1>
    <div class="intro">
      O(A) cliente realizou uma compra na <strong>{{ $coupon['enterprise']->name }}</strong> em {{ $formattedDate }}.  
      Este documento comprova a transação realizada e apresenta os detalhes dos produtos adquiridos, incluindo quantidades e valores correspondentes.
    </div>
  <div class="card">
    <div class="header">
      <span class="enterprise-name">{{ strtoupper($coupon['enterprise']->name) }}</span>
      <span class="sale-date">{{ $formattedDate }}</span>
    </div>

    @if($coupon['enterprise']->cnpj || $coupon['enterprise']->cpf)
      <div>
        <strong>
          {{ $coupon['enterprise']->cnpj ? 'CNPJ: ' . $coupon['enterprise']->cnpj : 'CPF: ' . $coupon['enterprise']->cpf }}
        </strong>
      </div>
    @endif

    <div class="divider"></div>

    <h3 style="margin-bottom: 8px; text-align:center; text-transform: uppercase; letter-spacing: 1px;">Cupom Fiscal</h3>

    <table>
      <thead>
        <tr>
          <th>ITEM</th>
          <th>CÓDIGO</th>
          <th>PRODUTO</th>
          <th>QTD</th>
          <th class="text-right">VALOR</th>
        </tr>
      </thead>
      <tbody>
        @foreach($coupon['products'] as $index => $product)
          <tr>
            <td>{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $product->product_sku }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->quantity }}</td>
            <td class="text-right">
              R$ {{ number_format($product->product_price, 2, ',', '.') }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="divider"></div>

    <div class="totals">
      <div>
        <span><strong>TROCO:</strong> R$ {{ number_format($coupon['additional']->change, 2, ',', '.') }}</span>
        <span class="total">TOTAL: R$ {{ number_format($coupon['exchange']->difference_value, 2, ',', '.') }}</span>
      </div>
      <span><strong>TARIFAS:</strong> R$ {{ number_format($coupon['additional']->fees, 2, ',', '.') }}</span>
    </div>
  </div>

</body>
</html>
