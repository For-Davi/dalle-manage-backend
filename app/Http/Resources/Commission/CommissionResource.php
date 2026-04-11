<?php

namespace App\Http\Resources\Commission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),

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
            'return_id' => $this->return_id,
            'product_name' => $this->product_name,
            'seller_name' => $this->seller_name,
            'seller_email' => $this->seller_email,
            'percentage' => $this->percentage,
            'commission_value' => $this->commission_value,
        ];
    }
}
