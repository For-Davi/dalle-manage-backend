<?php

namespace App\Http\Resources\Product\Movement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexByVariantProductMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->translateReason($this->reason),
            'type' => $this->type === 'in' ? 'Entrada' : 'Saída',
            'document_number' => $this->document_number,
            'lot_number' => $this->lot_number,
            'quantity' => $this->quantity,
            'previous_stock' => $this->previous_stock,
            'new_stock' => $this->new_stock,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'description' => $this->description,
            'status' => $this->status === 'active' ? 'Ativa' : 'Cancelada',
            'return_id' => $this->return_id,
            'sale_id' => $this->sale_id,
            'created_by_name' => $this->created_by_name,
            'created_by_email' => $this->created_by_email,
            'updated_by_name' => $this->updated_by_name,
            'updated_by_email' => $this->updated_by_email,
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),
        ];
    }

    private function translateReason(string $reason): string
    {
        return match ($reason) {
            'sale' => 'Venda',
            'buy' => 'Compra',
            'return' => 'Devolução',
            'loss' => 'Perda/Avaria',
            'transfer_in' => 'Transferência de entrada',
            'transfer_out' => 'Transferência de saída',
            'adjustment_in' => 'Ajuste de entrada',
            'adjustment_out' => 'Ajuste de saída',
            'production' => 'Produção',
            'internal_use' => 'Consumo interno',
            default => $reason,
        };
    }
}
