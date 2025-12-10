<?php

namespace App\DTO\Supplier\Order\Item;

use App\DTO\BaseDTO;

class UpdateSupplierOrderItemReceivedDTO extends BaseDTO
{
    public function __construct(
        public readonly int $quantity_received,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            quantity_received: $data['received'],
        );
    }
}
