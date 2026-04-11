<?php

namespace App\DTO\Enterprise;

use App\DTO\BaseDTO;

class UpdateEnterpriseDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $cpf,
        public ?string $cnpj,
        public ?string $cep,
        public ?string $state,
        public ?string $city,
        public ?string $neighborhood,
        public ?string $address,
        public ?string $number_address,
        public ?string $complement,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            cpf: $data['cpf'],
            cnpj: $data['cnpj'],
            cep: $data['cep'],
            state: $data['state'],
            city: $data['city'],
            neighborhood: $data['neighborhood'],
            address: $data['address'],
            number_address: $data['numberAddress'],
            complement: $data['complement'],
        );
    }
}
