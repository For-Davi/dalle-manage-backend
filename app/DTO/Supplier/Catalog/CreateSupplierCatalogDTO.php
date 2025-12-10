<?php

namespace App\DTO\Supplier\Catalog;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

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
            enterprise_id: Auth::user()->enterprise_id,
            description: $data['description'] ?? null,
        );
    }
}
