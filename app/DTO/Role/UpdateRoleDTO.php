<?php

namespace App\DTO\Role;

use App\DTO\BaseDTO;

class UpdateRoleDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public readonly ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
        );
    }
}
