<?php

namespace App\DTO\Supplier\Order\Status;

use App\DTO\BaseDTO;
use App\Enums\Supplier\Order\SupplierOrderStatus;

class UpdateSupplierOrderStatusDTO extends BaseDTO
{
    public function __construct(
        public readonly SupplierOrderStatus $status,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            status: SupplierOrderStatus::from($data['status']),
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
        ];
    }
}
