<?php

namespace App\Http\Resources\Return;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowLinkedReturnProductsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'return_exchange_items' => $this->returnExchangeItems->map(function ($exchangeItem) {
                return [
                    'product_name' => $exchangeItem->product_name,
                    'product_sku' => $exchangeItem->product_sku,
                    'product_price' => $exchangeItem->product_price,
                    'quantity' => $exchangeItem->quantity,
                    'total' => $exchangeItem->total,
                    // 'code' => $exchangeItem->product_code,
                    'color' => $exchangeItem->product_color,
                    'color_name' => $exchangeItem->product_color_name,
                ];
            }),
        ];
    }
}
