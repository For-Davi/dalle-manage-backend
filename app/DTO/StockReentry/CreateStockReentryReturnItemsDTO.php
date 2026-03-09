<?php

namespace App\DTO\StockReentry;

use App\DTO\BaseDTO;

class CreateStockReentryReturnItemsDTO extends BaseDTO
{
    public function __construct(
        public int $product_variant_id,
        public int $enterprise_id,
        public string $product_name,
        public ?string $product_sku,
        public ?string $product_code,
        public ?string $product_color,
        public ?string $product_color_name,
        public int $quantity,
    ) {}

    public static function fromRequest($data, $enterpriseID): self
    {
        return new self(
            product_variant_id: $data['product_variant_id'],
            enterprise_id: $enterpriseID,
            product_name: $data['product_name'],
            product_sku: $data['product_sku'] ?? null,
            product_code: $data['product_code'] ?? null,
            product_color: $data['color'] ?? null,
            product_color_name: $data['color_name'] ?? null,
            quantity: $data['returnQuantity'],
        );
    }
}
