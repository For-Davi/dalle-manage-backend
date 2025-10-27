<?php

namespace App\DTO\Supplier\Order\Item;

use App\DTO\BaseDTO;

class CreateSupplierOrderItemDTO extends BaseDTO
{
    public function __construct(
        public readonly int $supplier_order_id,
        public readonly int $product_variant_id,
        public readonly int $quantity_requested,
        public readonly float $unit_cost,
        public readonly float $total_cost,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            supplier_order_id: $data['supplierOrderID'],
            product_variant_id: $data['productVariantID'],
            quantity_requested: $data['quantityRequested'],
            unit_cost: $data['unitCost'],
            total_cost: $data['totalCost'],
        );
    }
}
