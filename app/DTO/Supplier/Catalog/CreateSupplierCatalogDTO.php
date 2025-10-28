<?php

namespace App\DTO\Supplier\Catalog;

use App\DTO\BaseDTO;

class CreateSupplierCatalogDTO extends BaseDTO
{
    public function __construct(
        public float $price,
        public int $product_variant_id,
        public int $supplier_id,
        public int $enterprise_id,
        public ?string $description,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            price: $data['price'],
            product_variant_id: $data['productVariantID'],
            supplier_id: $data['supplierID'],
            enterprise_id: $data['enterpriseID'],
            description: $data['description'] ?? null,
        );
    }
}
