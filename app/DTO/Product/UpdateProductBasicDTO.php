<?php

namespace App\DTO\Product;

use App\DTO\BaseDTO;

class UpdateProductBasicDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?int $product_category_id,
        public readonly ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            description: $data['description'],
            product_category_id: $data['category'],
        );
    }
}
