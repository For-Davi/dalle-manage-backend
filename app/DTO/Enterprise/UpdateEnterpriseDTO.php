<?php

namespace App\DTO\Enterprise;

class UpdateEnterpriseDTO
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

    public static function fromRequest(array $data): self
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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cpf' => $this->cpf,
            'cnpj' => $this->cnpj,
            'cep' => $this->cep,
            'state' => $this->state,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'address' => $this->address,
            'number_address' => $this->number_address,
            'complement' => $this->complement,
        ];
    }
}
