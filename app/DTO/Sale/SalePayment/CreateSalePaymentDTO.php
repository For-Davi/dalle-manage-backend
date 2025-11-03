<?php

namespace App\DTO\Sale\SalePayment;

class CreateSalePaymentDTO
{
    public function __construct(
        public readonly int $sale_id,
        public readonly int $payment_method_id,
        public readonly int $receipt_id,
        public readonly ?int $installments,
        public readonly float $value,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['saleID'],
            payment_method_id: $data['paymentMethodID'],
            receipt_id: $data['receiptID'],
            installments: $data['installments'] ?? null,
            value: $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'sale_id' => $this->sale_id,
            'payment_method_id' => $this->payment_method_id,
            'receipt_id' => $this->receipt_id,
            'installments' => $this->installments,
            'value' => $this->value,
        ];
    }
}
