<?php

namespace App\DTO\Movement;

use App\DTO\BaseDTO;

class CreateOrUpdateMovementDTO extends BaseDTO
{
    public function __construct(
        public readonly string $date,
        public readonly string $type,
        public readonly ?int $transaction_category_id,
        public readonly float $value,
        public readonly ?string $description,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            date: $data['date'],
            type: $data['type'],
            transaction_category_id: $data['transactionCategoryID'],
            value: $data['value'],
            description: $data['description'],
            enterprise_id: $data['enterpriseID'],
        );
    }
}
