<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordDTO extends BaseDTO
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
