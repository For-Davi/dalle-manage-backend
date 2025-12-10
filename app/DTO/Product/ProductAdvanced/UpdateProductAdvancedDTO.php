<?php

namespace App\DTO\Product\ProductAdvanced;

use App\DTO\BaseDTO;

class UpdateProductAdvancedDTO extends BaseDTO
{
    public function __construct(
        public readonly int $active,
        public readonly int $allow_coupon,
        public readonly int $allow_discount,
        public readonly int $discount_max_percentage,
        public readonly int $has_commission,
        public readonly int $commission_percentage,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            active: $data['active'],
            allow_coupon: $data['allowCoupon'],
            allow_discount: $data['allowDiscount'],
            discount_max_percentage: $data['discountMaxPercentage'],
            has_commission: $data['hasCommission'],
            commission_percentage: $data['commissionPercentage'],
        );
    }
}
