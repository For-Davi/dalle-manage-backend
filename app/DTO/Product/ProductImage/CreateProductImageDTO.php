<?php

namespace App\DTO\Product\ProductImage;

use App\DTO\BaseDTO;

class CreateProductImageDTO extends BaseDTO
{
    public function __construct(
        public readonly int $image_id,
        public readonly int $product_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            image_id: $data['imageID'],
            product_id: $data['productID'],
        );
    }
}
