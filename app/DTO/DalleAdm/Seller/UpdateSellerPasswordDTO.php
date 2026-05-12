<?php

namespace App\DTO\DalleAdm\Seller;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Hash;

class UpdateSellerPasswordDTO extends BaseDTO
{
    public function __construct(
        public string $password,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            password: Hash::make($data['newPassword']),
        );
    }
}
