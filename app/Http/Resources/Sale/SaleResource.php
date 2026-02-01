<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'seller_name' => $this->seller_name,
            'client_name' => $this->client_name,
            'status' => $this->status,
            'fees' => $this->fees,
            'starting_total' => $this->starting_total,
            'current_total' => $this->current_total,
            'change' => $this->change,
            'date' => $this->date,
            'sale_itens' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'grid' => $item->product_grid_name,
                    'code' => $item->product_code,
                    'color' => $item->product_color,
                    'color_name' => $item->product_color_name,
                ];
            }),
            'sale_payments_methods' => $this->payment->map(function ($payment) {
                return [
                    'value' => $payment->value,
                    'installments' => $payment->installments,
                    'type' => $payment->type->name,
                    'receipt' => $payment->receipt->identifier,
                ];
            }),
            'sale_delivery' => $this->delivery,
        ];
    }
}
