<?php

namespace App\DTO\Sale\SaleItem;

class UpdateSaleItemDTO
{
    public function __construct(
        public readonly int $delivered,
        public readonly int $quantity_delivered,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            delivered: $data['delivered'],
            quantity_delivered: $data['quantityDelivered'],
        );
    }

    public function toArray(): array
    {
        return [
            'delivered' => $this->delivered,
            'quantity_delivered' => $this->quantity_delivered,
        ];
    }
}
