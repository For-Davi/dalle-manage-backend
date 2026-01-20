<?php

namespace App\DTO\Commission;

use App\DTO\BaseDTO;

class CreateCommissionDTO extends BaseDTO
{
    public function __construct(
        public int $sale_id,
        public string $type,
        public string $status,
        public int $product_id,
        public string $product_name,
        public int $seller_id,
        public string $seller_name,
        public ?string $seller_email,
        public float $percentage,
        public float $commission_value,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['sale_id'],
            type: $data['type'],
            status: $data['status'],
            product_id: $data['product_id'],
            product_name: $data['product_name'],
            seller_id: $data['seller_id'],
            seller_name: $data['seller_name'],
            seller_email: $data['seller_email'],
            percentage: $data['percentage'],
            commission_value: $data['commission_value'],
        );
    }
}
