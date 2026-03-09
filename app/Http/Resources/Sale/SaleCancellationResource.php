<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleCancellationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_by_name' => $this->created_by_name,
            'created_by_email' => $this->created_by_email,
            'reason' => $this->reason,
            'description' => $this->description,
            'created_at' => $this->created_at?->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s'),
        ];
    }
}
