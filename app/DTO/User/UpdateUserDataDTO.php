<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;

class UpdateUserDataDTO extends BaseDTO
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
}
