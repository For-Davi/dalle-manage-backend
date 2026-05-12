<?php

namespace App\DTO\SellerRegistration;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Hash;

class CreateSellerRegistrationDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $cpf,
        public string $password,
        public ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            cpf: $data['cpf'],
            password: Hash::make($data['password']),
            description: $data['description'],
        );
    }
}
