<?php

namespace App\DTO\Exchange\ExchangePayment;

class CreateExchangePaymentMethodDTO
{
    public function __construct(
        public readonly int $return_id,
        public readonly int $receipt_id,
        public readonly string $receipt_name,
        public readonly int $payment_method_id,
        public readonly float $value,
    ) {}

    public static function fromRequest($data, $returnID, $receiptName, $paymentMethodID): self
    {
        return new self(
            return_id: $returnID,
            receipt_id: $data['receiptID'],
            receipt_name: $receiptName,
            payment_method_id: $paymentMethodID,
            value: $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'return_id' => $this->return_id,
            'payment_method_id' => $this->payment_method_id,
            'receipt_id' => $this->receipt_id,
            'receipt_name' => $this->receipt_name,
            'value' => $this->value,
        ];
    }
}
