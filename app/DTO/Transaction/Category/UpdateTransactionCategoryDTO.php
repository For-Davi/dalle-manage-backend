<?php

namespace App\DTO\Transaction\Category;

use App\DTO\BaseDTO;

class UpdateTransactionCategoryDTO extends BaseDTO
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
