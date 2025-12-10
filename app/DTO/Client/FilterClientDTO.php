<?php

namespace App\DTO\Client;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterClientDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?int $cpf,
        public readonly ?int $cnpj,
        public readonly ?string $country,
        public readonly ?string $state,
        public readonly ?string $city,
        public readonly ?int $enterprise_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] !== '' ? $data['name'] : null,
            email: $data['email'] !== '' ? $data['email'] : null,
            cpf: $data['cpf'] !== '' ? $data['cpf'] : null,
            cnpj: $data['cnpj'] !== '' ? $data['cnpj'] : null,
            country: $data['country'] !== '' ? $data['country'] : null,
            state: $data['state'] !== '' ? $data['state'] : null,
            city: $data['city'] !== '' ? $data['city'] : null,
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
