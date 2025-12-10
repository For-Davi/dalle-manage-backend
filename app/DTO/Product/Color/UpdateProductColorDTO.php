<?php

namespace App\DTO\Product\Color;

use App\DTO\BaseDTO;

class UpdateProductColorDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $hex_color_code,
        public readonly int $active,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            hex_color_code: $data['hexColorCode'],
            active: $data['active'],
        );
    }
}
