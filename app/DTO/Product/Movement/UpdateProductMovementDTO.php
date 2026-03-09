<?php

namespace App\DTO\Product\Movement;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class UpdateProductMovementDTO extends BaseDTO
{
    public function __construct(
        public readonly string $status,
        public readonly int $updated_by,
        public readonly string $updated_by_name,
        public readonly string $updated_by_email,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            status: $data['status'],
            updated_by: Auth::user()->id,
            updated_by_name: Auth::user()->name,
            updated_by_email: Auth::user()->email,
        );
    }
}
