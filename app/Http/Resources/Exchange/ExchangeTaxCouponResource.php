<?php

namespace App\Http\Resources\Exchange;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeTaxCouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'exchange' => [
                'id' => $this->id,
                'fees' => $this->additionalExchange?->fees,
                'total' => $this->difference_value,
                'change' => $this->additionalExchange?->change,
                'date' => $this->created_at?->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s'),
            ],
            'enterprise' => [
                'id' => $this->sale?->enterprise?->id,
                'name' => $this->sale?->enterprise?->name,
                'cpf' => $this->sale?->enterprise?->cpf,
                'cnpj' => $this->sale?->enterprise?->cnpj,
            ],
            'products' => $this->return?->returnExchangeItems
                ? $this->return->returnExchangeItems->map(function ($item) {
                    return [
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'product_price' => $item->product_price,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                    ];
                })
                : [],
        ];
    }
}
