<?php

namespace App\DTO\DalleAdm\Commission;

use App\DTO\BaseDTO;

class CreateCommissionDTO extends BaseDTO
{
    public function __construct(
        public int $enterprise_id,
        public string $enterprise_name,
        public ?string $enterprise_email,
        public int $seller_id,
        public string $seller_name,
        public string $seller_cpf,
        public string $seller_email,
        public float $percentage,
        public float $commission_value,
    ) {}

    public static function fromRequest($data, $enterprise, $commission_value): self
    {
        return new self(
            enterprise_id: $enterprise['id'],
            enterprise_name: $enterprise['name'],
            enterprise_email: $enterprise['email'] ?? null,
            seller_id: $data['id'],
            seller_name: $data['name'],
            seller_cpf: $data['cpf'],
            seller_email: $data['email'],
            percentage: $data['commission'],
            commission_value: $commission_value,
        );
    }
}
