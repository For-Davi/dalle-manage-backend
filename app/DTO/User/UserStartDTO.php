<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserStartDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public int $enterprise_id,
        public int $role_id
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: Hash::make($data['password']),
            role_id: $data['roleID'],
            enterprise_id: $data['enterpriseID'],
        );
    }
}
