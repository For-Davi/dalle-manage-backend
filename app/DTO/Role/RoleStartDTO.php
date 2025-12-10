<?php

namespace App\DTO\Role;

use App\DTO\BaseDTO;

class RoleStartDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public array $permissions,
        public string $enterprise_id
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: 'Master',
            enterprise_id: $data['enterprise_id'],
            permissions: []
        );
    }
}
