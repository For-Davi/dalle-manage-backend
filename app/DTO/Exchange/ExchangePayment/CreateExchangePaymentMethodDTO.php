<?php

namespace App\DTO\Exchange\ExchangePayment;

class CreateExchangePaymentMethodDTO
{
    public function __construct(
        public readonly int $exchange_id,
        public readonly int $receipt_id,
        public readonly string $receipt_name,
        public readonly int $payment_method_id,
        public readonly float $value,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            exchange_id: $data['exchangeID'],
            receipt_id: $data['receiptID'],
            receipt_name: $data['receiptName'],
            payment_method_id: $data['paymentMethodID'],
            value: $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'exchange_id' => $this->exchange_id,
            'payment_method_id' => $this->payment_method_id,
            'receipt_id' => $this->receipt_id,
            'receipt_name' => $this->receipt_name,
            'value' => $this->value,
        ];
    }
}
