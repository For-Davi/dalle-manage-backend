<?php

namespace App\DTO\Supplier\Order;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateSupplierOrderReceivingDTO extends BaseDTO
{
    public function __construct(
        public readonly int $supplier_order_item_id,
        public readonly int $quantity_received,
        public readonly string $receiving_date,
        public readonly int $received_by,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            supplier_order_item_id: $data['supplierOrderItemID'],
            quantity_received: $data['quantityReceived'],
            receiving_date: $data['receivingDate'],
            received_by: Auth::user()->id,
        );
    }
}
