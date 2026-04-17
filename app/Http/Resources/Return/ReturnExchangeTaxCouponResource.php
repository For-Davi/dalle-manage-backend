<?php

namespace App\Http\Resources\Return;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnExchangeTaxCouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $enterprise = $this->sale?->enterprise;

        return [
            'return' => [
                'id' => $this->id,
                'sale_code' => $this->sale_id,
                'exchange_value' => $this->exchange_value,
                'difference_value' => $this->difference_value,
                'current_value' => $this->current_value,
                'fees' => $this->fees,
                'change' => $this->change,
                'freight_fees' => $this->freight_fees,
                'freight_change' => $this->freight_change,
                'freight_value' => $this->delivery?->freight_value,
                'date' => $this->created_at?->format('d/m/Y H:i:s'),
            ],

            'enterprise' => [
                'name' => $enterprise?->name,
                'cpf' => $enterprise?->cpf,
                'cnpj' => $enterprise?->cnpj,
            ],

            'products' => $this->returnExchangeItems
                ? $this->returnExchangeItems->map(function ($item) {
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
