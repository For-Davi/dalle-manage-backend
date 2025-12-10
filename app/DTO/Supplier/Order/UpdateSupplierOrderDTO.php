<?php

namespace App\DTO\Supplier\Order;

use App\DTO\BaseDTO;

class UpdateSupplierOrderDTO extends BaseDTO
{
    public function __construct(
        public readonly int $supplier_id,
        public readonly ?string $date_delivery_expected,
        public readonly ?string $date_issue,
        public readonly ?string $order_number,
        public readonly ?string $observation,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            supplier_id: $data['supplierID'],
            date_delivery_expected: $data['dateDeliveryExpected'],
            date_issue: $data['dateIssue'],
            order_number: $data['orderNumber'],
            observation: $data['observation'],
        );
    }
}
