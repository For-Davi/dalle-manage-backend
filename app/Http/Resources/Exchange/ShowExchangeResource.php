<?php

namespace App\Http\Resources\Exchange;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowExchangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'return_id' => $this->return_id,
            'status' => match ($this->status) {
                'active' => 'Ativo',
                'cancelled' => 'Cancelado',
                default => $this->status,
            },
            'exchange_value' => $this->exchange_value,
            'difference_value' => $this->difference_value,
            'created_by_name' => $this->created_by_name,
            'created_by_email' => $this->created_by_email,
            'updated_by_name' => $this->updated_by_name,
            'updated_by_email' => $this->updated_by_email,
            'created_at' => $this->created_at
                ->timezone('America/Sao_Paulo')
                ->format('d/m/Y H:i:s'),
            'exchange_payment_method' => $this->paymentExchange
                ? $this->paymentExchange->map(function ($payment) {
                    return [
                        'receipt_name' => $payment->receipt_name,
                        'type_receipt_name' => $payment->type->name,
                        'value' => $payment->value,
                    ];
                })->values()
                : [],

            'difference_payment_method' => $this->paymentDifference
                ? $this->paymentDifference->map(function ($payment) {
                    return [
                        'receipt_name' => $payment->receipt_name,
                        'type_receipt_name' => $payment->type->name,
                        'installments' => $payment->installments,
                        'value' => $payment->value,
                    ];
                })->values()
                : [],
        ];
    }
}
