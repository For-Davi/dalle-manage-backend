<?php

namespace App\Http\Resources\Return;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => match ($this->status) {
                'active' => 'Ativa',
                'cancelled' => 'Cancelada',
                default => $this->status,
            },
            'linked_return_id' => $this->linked_return_id,
            'created_by_name' => $this->created_by_name,
            'created_by_email' => $this->created_by_email,
            'updated_by_name' => $this->updated_by_name,
            'updated_by_email' => $this->updated_by_email,
            'seller_name' => $this->seller_name,
            'seller_email' => $this->seller_email,
            'exchange_value' => $this->exchange_value,
            'difference_value' => $this->difference_value,
            'current_value' => $this->current_value,
            'freight_fees' => $this->freight_fees,
            'freight_change' => $this->freight_change,
            'fees' => $this->fees,
            'change' => $this->change,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s'),
            'return_items' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'product_code' => $item->product_code,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'color' => $item->product_color,
                    'color_name' => $item->product_color_name,
                    'reason' => $item->reason,
                    'description' => $item->description,
                ];
            }),
            'return_exchange_items' => $this->returnExchangeItems->map(function ($exchangeItem) {
                return [
                    'product_name' => $exchangeItem->product_name,
                    'product_sku' => $exchangeItem->product_sku,
                    'product_code' => $exchangeItem->product_code,
                    'product_price' => $exchangeItem->product_price,
                    'quantity' => $exchangeItem->quantity,
                    'total' => $exchangeItem->total,
                    'color' => $exchangeItem->product_color,
                    'color_name' => $exchangeItem->product_color_name,
                ];
            }),
            'exchange_payment_methods' => $this->exchangePaymentMethod->map(function ($payment){
                return [
                    'value' => $payment->value,
                    'installments' => $payment->installments,
                    'type' => $payment->type->name,
                    'receipt' => $payment->receipt_name,
                ];
            }),
            'sale_payment_methods' => $this->salePaymentMethod->map(function ($payment){
                return [
                    'value' => $payment->value,
                    'installments' => $payment->installments,
                    'type' => $payment->type->name,
                    'receipt' => $payment->receipt_name,
                ];
            }),
            'delivery' => $this->delivery,
        ];
    }
}
