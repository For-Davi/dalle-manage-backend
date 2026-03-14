<?php

namespace App\DTO\Sale;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterSaleDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $start_date,
        public readonly ?string $end_date,
        public readonly ?string $status,
        public readonly ?int $client,
        public readonly ?int $seller,
        public readonly ?string $product,
        public readonly ?int $type_receipt,
        public readonly ?int $receipt,
        public readonly ?float $min_total,
        public readonly ?float $max_total,
        public readonly ?int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            start_date: $data['startDate'] !== '' ? $data['startDate'] : null,
            end_date: $data['endDate'] !== '' ? $data['endDate'] : null,
            status: $data['status'] !== '' ? $data['status'] : null,
            client: $data['client'] ? $data['client'] : null,
            seller: $data['seller'] ? $data['seller'] : null,
            product: $data['product'] !== '' ? $data['product'] : null,
            type_receipt: $data['paymentType'] ? $data['paymentType'] : null,
            receipt: $data['receipt'] ? $data['receipt'] : null,
            min_total: $data['minTotal'] ? $data['minTotal'] : null,
            max_total: $data['maxTotal'] ? $data['maxTotal'] : null,
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
