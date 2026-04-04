<?php

namespace App\DTO\DeliveryGuy;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateOrUpdateDeliveryGuyDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?int $cpf,
        public readonly string $vehicle,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            cpf: $data['cpf'],
            vehicle: $data['vehicle'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
