<?php

namespace App\DTO\Commission;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterCommissionDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $start_period,
        public readonly ?string $end_period,
        public readonly ?int $seller_id,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            start_period: $data['startPeriod'],
            end_period: $data['endPeriod'],
            seller_id: $data['sellerID'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
