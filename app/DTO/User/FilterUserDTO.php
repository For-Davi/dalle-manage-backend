<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterUserDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?int $active,
        public readonly ?int $department_id,
        public readonly ?int $role_id,
        public readonly ?int $enterprise_id
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'] !== '' ? $data['name'] : null,
            email: $data['email'] !== '' ? $data['email'] : null,
            department_id: $data['department'],
            enterprise_id: Auth::user()->enterprise_id,
            role_id: $data['role'],
            active: $data['active']
        );
    }
}
