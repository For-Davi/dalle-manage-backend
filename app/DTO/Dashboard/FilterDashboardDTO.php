<?php

namespace App\DTO\Dashboard;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterDashboardDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $start_date,
        public readonly ?string $end_date,
        public readonly ?int $seller,
        public readonly ?int $category,
        public readonly ?string $product,
        public readonly ?int $type_receipt,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            start_date: $data['startDate'] !== '' ? $data['startDate'] : null,
            end_date: $data['endDate'] !== '' ? $data['endDate'] : null,
            seller: $data['seller'],
            category: $data['category'],
            product: $data['product'],
            type_receipt: $data['typeReceipt'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
