<?php

namespace App\DTO\Return;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class UpdateReturnDTO extends BaseDTO
{
    public function __construct(
        public string $status,
        public string $updated_by_name,
        public string $updated_by_email,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            status: $data['status'] === 'Ativa' ? 'active' : 'canceled',
            updated_by_name: Auth::user()->name,
            updated_by_email: Auth::user()->email,
        );
    }
}
