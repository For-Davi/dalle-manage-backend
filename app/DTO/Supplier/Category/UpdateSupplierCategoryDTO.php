<?php

namespace App\DTO\Supplier\Category;

use App\DTO\BaseDTO;

class UpdateSupplierCategoryDTO extends BaseDTO
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
