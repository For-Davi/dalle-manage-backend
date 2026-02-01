<?php

namespace App\Http\Resources\Exchange;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'return_id' => $this->return_id,
            'status' => match ($this->status) {
                'active' => 'Ativo',
                'cancelled' => 'Cancelado',
                default => $this->status,
            },
            'exchange_value' => $this->exchange_value,
            'difference_value' => $this->difference_value,
            'created_by_name' => $this->created_by_name,
            'updated_by_name' => $this->updated_by_name,
            'created_at' =>  $this->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i:s'),
            'exchange_payments_methods' => $this->payment->map(function ($payment) {
                return [
                    'receipt_name' => $payment->receipt_name,
                    'type_receipt_name' => $payment->name,
                    'value' => $payment->value,
                    'change' => $payment->change,
                ];
            }),
        ];
    }
}
