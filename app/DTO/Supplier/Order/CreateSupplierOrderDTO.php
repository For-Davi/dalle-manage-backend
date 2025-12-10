<?php

namespace App\DTO\Supplier\Order;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateSupplierOrderDTO extends BaseDTO
{
    public function __construct(
        public readonly int $supplier_id,
        public readonly int $enterprise_id,
        public readonly ?string $date_delivery_expected,
        public readonly ?string $date_issue,
        public readonly ?string $order_number,
        public readonly int $created_by,
        public readonly ?string $observation,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            supplier_id: $data['supplierID'],
            enterprise_id: Auth::user()->enterprise_id,
            date_delivery_expected: $data['dateDeliveryExpected'],
            date_issue: $data['dateIssue'],
            order_number: $data['orderNumber'] ?? null,
            created_by: Auth::user()->id,
            observation: $data['observation'] ?? null,
        );
    }
}
