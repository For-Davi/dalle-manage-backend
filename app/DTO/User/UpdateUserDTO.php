<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;

class UpdateUserDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $department_id,
        public string $role_id,
        public int $active
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            department_id: $data['departmentId'],
            role_id: $data['roleId'],
            active: $data['active'],
        );
    }
}
