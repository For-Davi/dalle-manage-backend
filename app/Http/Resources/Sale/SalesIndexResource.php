<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seller_name' => $this->seller_name,
            'client_name' => $this->client_name,
            'fees' => $this->fees,
            'total' => $this->total,
            'change' => $this->change,
            'date' => $this->date,
        ];
    }
}
