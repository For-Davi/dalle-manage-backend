<?php

namespace App\DTO\Exchange;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateExchangeDTO extends BaseDTO
{
    public function __construct(
        public int $sale_id,
        public int $return_id,
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
            return_id: $data['returnID'],
            status: 'active',
            exchange_value: $data['exchangeValue'],
            difference_value: $data['differenceValue'],
            created_by_name: Auth::user()->name,
            created_by_email: Auth::user()->email,
            updated_by_name: null,
            updated_by_email: null,
        );
    }
}
