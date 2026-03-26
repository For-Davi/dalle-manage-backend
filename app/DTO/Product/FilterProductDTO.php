<?php

namespace App\DTO\Product;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterProductDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $sku,
        public readonly ?int $stockCritical,
        public readonly ?int $categoryID,
        public readonly ?int $active,
        public readonly ?int $enterpriseID,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            sku: $data['sku'],
            stockCritical: $data['stockCritical'],
            categoryID: $data['category'],
            enterpriseID: Auth::user()->enterprise_id,
            active: $data['active']
        );
    }
}
