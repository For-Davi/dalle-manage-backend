<?php

namespace App\DTO\Return\ExchangeReturnItem;

use App\DTO\BaseDTO;

class CreateExchangeReturnItemDTO extends BaseDTO
{
    public function __construct(
        public int $return_id,
        public int $product_variant_id,
        public string $product_name,
        public ?string $product_sku,
        public float $product_price,
        public ?string $product_color,
        public ?string $product_color_name,
        public int $quantity,
        public float $total,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            return_id: $data['returnID'],
            product_variant_id: $data['productVariantID'],
            product_name: $data['productName'],
            product_sku: $data['productSKU'] ?? null,
            product_price: $data['productPrice'],
            product_color: $data['productColor'] ?? null,
            product_color_name: $data['productColorName'] ?? null,
            quantity: $data['quantity'],
            total: $data['total'],
        );
    }
}
