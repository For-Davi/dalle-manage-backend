<?php

namespace App\DTO\Product;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateProductDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?int $product_category_id,
        public readonly ?string $description,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['basic']['name'],
            type: $data['basic']['type'],
            description: $data['basic']['description'],
            product_category_id: $data['basic']['categoryID'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
