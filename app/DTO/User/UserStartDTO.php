<?php

namespace App\DTO\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserStartDTO
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
            role_id: $data['roleId'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
