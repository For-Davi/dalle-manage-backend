<?php

namespace App\Http\Resources\Return;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => match ($this->status) {
                'active' => 'Ativa',
                'canceled' => 'Cancelada',
                default => $this->status,
            },
            'linked_return_id' => $this->linked_return_id,
            'created_by_name' => $this->created_by_name,
            'created_by_email' => $this->created_by_email,
            'updated_by_name' => $this->updated_by_name,
            'updated_by_email' => $this->updated_by_email,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'return_exchange_items' => $this->returnExchangeItems->map(function ($exchangeItem) {
                return [
                    'product_name' => $exchangeItem->product_name,
                    'product_sku' => $exchangeItem->product_sku,
                    'product_price' => $exchangeItem->product_price,
                    'quantity' => $exchangeItem->quantity,
                    'total' => $exchangeItem->total,
                    'color' => $exchangeItem->product_color,
                    'color_name' => $exchangeItem->product_color_name,
                ];
            }),
        ];
    }
}
