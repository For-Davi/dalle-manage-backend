<?php

namespace App\DTO\Supplier\Order\Status;

use App\DTO\BaseDTO;
use App\Enums\Supplier\Order\SupplierOrderStatus;
use Illuminate\Support\Facades\Auth;

class CreateSupplierOrderStatusHistoryDTO extends BaseDTO
{
    public function __construct(
        public readonly int $supplier_order_id,
        public readonly SupplierOrderStatus $status,
        public readonly int $changed_by,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            supplier_order_id: $data['orderID'],
            status: SupplierOrderStatus::from($data['status']),
            changed_by: Auth::id(),
        );
    }

    public function toArray(): array
    {
        return [
            'supplier_order_id' => $this->supplier_order_id,
            'status' => $this->status->value,
            'changed_by' => $this->changed_by,
        ];
    }
}
