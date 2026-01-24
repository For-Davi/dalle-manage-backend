<?php

namespace App\DTO\Exchange;

use Illuminate\Support\Facades\Auth;
use App\DTO\BaseDTO;

class CreateExchangeDTO extends BaseDTO
{
    public function __construct(
        public int $sale_id,
        public string $status,
        public float $exchange_value,
        public float $difference_value,
        public string $created_by_name,
        public string $created_by_email,
        public ?string $updated_by_name,
        public ?string $updated_by_email,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['saleID'],
            status: 'active',
            exchange_value: $data[''],
            difference_value: 'active',
            created_by_name: Auth::user()->name,
            created_by_email: Auth::user()->email,
            updated_by_name: null,
            updated_by_email: null,
        );
    }
}
