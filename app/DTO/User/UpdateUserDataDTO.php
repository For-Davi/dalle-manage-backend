<?php

namespace App\DTO\User;

class UpdateUserDataDTO
{
    public function __construct(
        public ?string $name,
        public ?string $email,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
