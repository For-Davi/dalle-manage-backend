<?php

namespace App\DTO\Supplier;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateSupplierDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public readonly ?string $email,
        public readonly ?int $cpf,
        public readonly ?int $cnpj,
        public readonly ?string $state_registration,
        public readonly ?string $municipal_registration,
        public readonly ?string $phone,
        public readonly ?string $site,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $city,
        public readonly ?int $cep,
        public readonly ?string $neighborhood,
        public readonly ?string $address,
        public readonly ?int $number,
        public readonly ?int $supplier_category_id,
        public readonly ?string $description,
        public readonly ?string $complement,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            cpf: $data['cpf'],
            cnpj: $data['cnpj'],
            state_registration: $data['stateRegistration'],
            municipal_registration: $data['municipalRegistration'],
            phone: $data['phone'],
            site: $data['site'],
            country: $data['country'],
            state: $data['state'],
            city: $data['city'],
            cep: $data['cep'],
            neighborhood: $data['neighborhood'],
            address: $data['address'],
            number: $data['number'],
            supplier_category_id: $data['categorySupplierId'],
            description: $data['description'],
            complement: $data['complement'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
