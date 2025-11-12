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

    public static function fromRequest(array $data): self
    {
        $quantity = (int) $data['quantityRequested'];
        $unitCost = (float) $data['unitCost'];
        $total = $quantity * $unitCost;

        return new self(
            supplier_order_id: $data['supplierOrderID'],
            product_variant_id: $data['productVariantID'],
            quantity_requested: $quantity,
            unit_cost: $unitCost,
            total_cost: $total,
        );
    }
}
