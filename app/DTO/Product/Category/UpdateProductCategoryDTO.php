<?php

namespace App\DTO\Product\Category;

use App\DTO\BaseDTO;

class UpdateProductCategoryDTO extends BaseDTO
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
