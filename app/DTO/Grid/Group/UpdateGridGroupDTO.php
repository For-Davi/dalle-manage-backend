<?php

namespace App\DTO\Grid\Group;

use App\DTO\BaseDTO;

class UpdateGridGroupDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public int $active,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['gridName'],
            active: $data['active'],
        );
    }
}
