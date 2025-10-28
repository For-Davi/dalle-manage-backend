<?php

namespace App\DTO\Receipt\Type;

use App\DTO\BaseDTO;

class UpdateTypeReceiptDTO extends BaseDTO
{
    public function __construct(
        public string $name,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
        );
    }
}
