<?php

namespace App\DTO\Exchange;

use Illuminate\Support\Facades\Auth;
use App\DTO\BaseDTO;

class UpdateExchangeDTO extends BaseDTO
{
    public function __construct(
        public string $status,
        public string $updated_by_name,
        public string $updated_by_email,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            status: $data['status'] === 'Ativa' || $data['status'] === 'Ativo' ? 'active' : 'canceled',
            updated_by_name: Auth::user()->name,
            updated_by_email: Auth::user()->email,
        );
    }
}
