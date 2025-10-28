<?php

namespace App\DTO\Tag;

use App\DTO\BaseDTO;

class UpdateTagDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $active,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            active: $data['active'],
        );
    }
}
