<?php

namespace App\Http\Resources\Commission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at
                ? $this->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i:s')
                : null,

            'status' => match ($this->status) {
                'active' => 'Ativa',
                'cancelled' => 'Cancelada',
                default => $this->status,
            },

            'type' => match ($this->type) {
                'sale' => 'Venda',
                'return' => 'Devolução',
                default => $this->type,
            },

            'product_name' => $this->product_name,
            'seller_name' => $this->seller_name,
            'seller_email' => $this->seller_email,
            'percentage' => $this->percentage,
            'commission_value' => $this->commission_value,
        ];
    }
}
