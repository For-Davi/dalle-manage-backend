<?php

namespace App\DTO\Receipt;

use App\DTO\BaseDTO;

class UpdateReceiptDTO extends BaseDTO
{
    public function __construct(
        public readonly string $identifier,
        public readonly ?int $type_receipt_id,
        public readonly int $active,
        public readonly ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            identifier: $data['identifier'],
            type_receipt_id: $data['typesID'] ?? null,
            active: $data['active'],
            description: $data['description'] ?? null
        );
    }
}
