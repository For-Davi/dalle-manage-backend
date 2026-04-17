<?php

namespace App\DTO\Return;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateReturnDTO extends BaseDTO
{
    public function __construct(
        public int $sale_id,
        public ?int $linked_return_id,
        public string $status,
        public float $exchange_value,
        public float $difference_value,
        public float $current_value,
        public float $fees,
        public float $change,
        public string $created_by_name,
        public string $created_by_email,
        public ?string $updated_by_name,
        public ?string $updated_by_email,
        public ?int $seller_id,
        public ?string $seller_name,
        public ?string $seller_email,
        public float $freight_fees,
        public float $freight_change,
    ) {}

    public static function fromRequest($data, $sellerName = null, $sellerEmail = null, $exchangeOrDifferenceCurrentValue): self
    {
        return new self(
            sale_id: $data['saleID'],
            linked_return_id: $data['returnID'] ?? null,
            status: 'active',
            exchange_value: $data['exchangeData']['exchangeValue'],
            difference_value: $data['exchangeData']['exchangeValue'] > 0 ? 0 : $data['exchangeData']['differenceValue'] + $data['paymentData']['paymentExchangeOrDifferenceData']['fees'] + (float) $data['paymentData']['deliveryData']['freightValue'] + $data['paymentData']['freightPaymentData']['fees'],
            current_value: $exchangeOrDifferenceCurrentValue < 0 ? 0 : $exchangeOrDifferenceCurrentValue,
            fees: $data['paymentData']['paymentExchangeOrDifferenceData']['fees'],
            change: $data['paymentData']['paymentExchangeOrDifferenceData']['change'],
            created_by_name: Auth::user()->name,
            created_by_email: Auth::user()->email,
            updated_by_name: null,
            updated_by_email: null,
            seller_id: $data['sellerID'] ?? null,
            seller_name: $sellerName ?? null,
            seller_email: $sellerEmail ?? null,
            freight_fees: $data['paymentData']['freightPaymentData']['fees'],
            freight_change: $data['paymentData']['freightPaymentData']['change'],
        );
    }
}
