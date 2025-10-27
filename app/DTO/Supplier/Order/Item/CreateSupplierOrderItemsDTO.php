<?php

namespace App\DTO\Supplier\Order\Item;

class CreateSupplierOrderItemDTO
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

    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
