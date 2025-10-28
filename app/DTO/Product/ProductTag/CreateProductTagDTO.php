<?php

namespace App\DTO\Product\ProductTag;

use App\DTO\BaseDTO;

class CreateProductTagDTO extends BaseDTO
{
    public function __construct(
        public readonly int $tag_id,
        public readonly int $product_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            tag_id: $data['tagID'],
            product_id: $data['productID'],
        );
    }
}
