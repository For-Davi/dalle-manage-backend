<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantTableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'name' => $this->product?->name,
            'price' => $this->price,
            'offer' => $this->offer,
            'stock_quantity' => $this->stock_quantity,
            'sku' => $this->sku,
            'variant_active' => $this->active,
            'code' => $this->code,
            'color' => $this->color ? [
                'name' => $this->color->name,
                'hex_color_code' => $this->color->hex_color_code,
            ] : null,
            'catalog' => $this->suppliers->first() ? [
                'supplier_id' => $this->suppliers->first()->pivot->supplier_id,
                'supplier_name' => $this->suppliers->first()->name,
                'price' => $this->suppliers->first()->pivot->price ?? null,
                'description' => $this->suppliers->first()->pivot->description ?? null,
            ] : null,
            'grid_item' => $this->gridItem ? [
                'id' => $this->gridItem->id,
                'size' => $this->gridItem->size,
                'grid_group' => $this->gridItem->gridGroup ? [
                    'id' => $this->gridItem->gridGroup->id,
                    'name' => $this->gridItem->gridGroup->name,
                ] : null,
            ] : null,
        ];
    }
}
