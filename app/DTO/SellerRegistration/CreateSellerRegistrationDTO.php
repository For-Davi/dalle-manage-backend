<?php

namespace App\DTO\SellerRegistration;

use App\DTO\BaseDTO;

class CreateSellerRegistrationDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            description: $data['description'],
        );
    }
}
