<?php

namespace App\DTO\Supplier;

use App\DTO\BaseDTO;

class FilterSupplierDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $city,
        public readonly ?int $active,
        public readonly ?int $category_id,
        public readonly ?int $enterprise_id,
        public readonly ?int $cpf,
        public readonly ?int $cnpj
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] !== '' ? $data['name'] : null,
            email: $data['email'] !== '' ? $data['email'] : null,
            cpf: $data['cpf'] !== '' ? $data['cpf'] : null,
            cnpj: $data['cnpj'] !== '' ? $data['cnpj'] : null,
            category_id: $data['category'],
            country: $data['country'] !== '' ? $data['country'] : null,
            state: $data['state'] !== '' ? $data['state'] : null,
            city: $data['city'] !== '' ? $data['city'] : null,
            enterprise_id: $data['enterprise_id'],
            active: $data['active']
        );
    }
}
