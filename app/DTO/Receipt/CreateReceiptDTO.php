<?php

namespace App\DTO\Receipt;

use App\DTO\BaseDTO;

class CreateReceiptDTO extends BaseDTO
{
    public function __construct(
        public readonly string $identifier,
        public readonly ?int $type_receipt_id,
        public readonly int $enterprise_id,
        public readonly ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            identifier: $data['identifier'],
            type_receipt_id: $data['typesID'] ?? null,
            enterprise_id: $data['enterpriseID'],
            description: $data['description']
        );
    }
}
