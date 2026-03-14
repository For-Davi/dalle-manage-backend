<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItensResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
            'product_price' => $this->product_price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'grid' => $this->product_grid_name,
            'product_code' => $this->product_code,
            'color' => $this->product_color,
            'color_name' => $this->product_color_name,
        ];
    }
}
