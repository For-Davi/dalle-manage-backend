<?php

namespace App\DTO\Product\ProductVariant;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateProductVariantDTO extends BaseDTO
{
    public function __construct(
        public readonly int $active,
        public readonly ?string $sku,
        public readonly ?string $description,
        public readonly ?string $location,
        public readonly int $enterprise_id,
        public readonly int $product_id,
        public readonly ?int $grid_item_id,
        public readonly ?int $color_id,
        public readonly float $price,
        public readonly ?float $offer,
        public readonly float $cost,
        public readonly float $stock_quantity,
        public readonly float $min_stock_alert
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            active: $data['active'],
            sku: $data['sku'],
            description: $data['description'],
            location: $data['location'],
            product_id: $data['productID'],
            grid_item_id: $data['gridItemID'],
            color_id: $data['colorID'],
            price: $data['price'],
            cost: $data['cost'],
            offer: $data['offer'],
            stock_quantity: $data['stockQuantity'],
            min_stock_alert: $data['minStockAlert'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
