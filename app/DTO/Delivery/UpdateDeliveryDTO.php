<?php

namespace App\DTO\Delivery;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class UpdateDeliveryDTO extends BaseDTO
{
    public function __construct(
        public readonly string $status,
        public readonly string $updated_by_name,
        public readonly string $updated_by_email,
    ) {}

    public static function fromRequest($status): self
    {
        return new self(
            status: $status,
            updated_by_name: Auth::user()->name,
            updated_by_email: Auth::user()->email
        );
    }
}
