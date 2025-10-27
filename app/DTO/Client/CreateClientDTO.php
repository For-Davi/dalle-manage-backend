<?php

namespace App\DTO\Client;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateClientDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public readonly ?string $email,
        public readonly ?string $sex,
        public readonly ?string $phone,
        public readonly ?int $cpf,
        public readonly ?int $cnpj,
        public readonly ?string $state_registration,
        public readonly ?string $municipal_registration,
        public readonly ?string $date_birthday,
        public readonly ?int $cep,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $city,
        public readonly ?string $neighborhood,
        public readonly ?string $address,
        public readonly ?int $number,
        public readonly ?string $complement,
        public readonly ?string $description,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            sex: $data['sex'],
            phone: $data['phone'],
            cpf: $data['cpf'],
            cnpj: $data['cnpj'],
            state_registration: $data['stateRegistration'],
            municipal_registration: $data['municipalRegistration'],
            date_birthday: $data['dateBirthday'],
            cep: $data['cep'],
            country: $data['country'],
            state: $data['state'],
            city: $data['city'],
            neighborhood: $data['neighborhood'],
            address: $data['address'],
            number: $data['number'],
            complement: $data['complement'],
            description: $data['description'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
