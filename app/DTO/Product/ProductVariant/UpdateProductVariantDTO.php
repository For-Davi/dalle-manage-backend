<?php

namespace App\DTO\Product\ProductVariant;

use App\DTO\BaseDTO;

class UpdateProductVariantDTO extends BaseDTO
{
    public function __construct(
        public readonly int $active,
        public readonly ?string $sku,
        public readonly ?string $description,
        public readonly ?string $location,
        public readonly float $price,
        public readonly float $offer,
        public readonly float $cost,
        public readonly float $min_stock_alert
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            active: $data['active'],
            sku: $data['sku'],
            description: $data['description'],
            location: $data['location'],
            price: $data['price'],
            cost: $data['cost'],
            offer: $data['offer'],
            min_stock_alert: $data['minStockAlert'],
        );
    }
}
