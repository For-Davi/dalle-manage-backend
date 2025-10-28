<?php

namespace App\DTO\Product\Category;

use App\DTO\BaseDTO;

class CreateProductCategoryDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            enterprise_id: $data['enterpriseID']
        );
    }
}
