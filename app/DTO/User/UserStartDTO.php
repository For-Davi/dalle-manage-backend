<?php

namespace App\DTO\User;

use Illuminate\Support\Facades\Hash;

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
            enterprise_id: $data['enterpriseID'],
            password: Hash::make($data['password']),
            role_id: $data['roleId']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role_id' => $this->role_id,
            'enterprise_id' => $this->enterprise_id,
        ];
    }
}
