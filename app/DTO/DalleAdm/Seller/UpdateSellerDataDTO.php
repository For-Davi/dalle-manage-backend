<?php

namespace App\DTO\DalleAdm\Seller;

use App\DTO\BaseDTO;

class UpdateSellerDataDTO extends BaseDTO
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
