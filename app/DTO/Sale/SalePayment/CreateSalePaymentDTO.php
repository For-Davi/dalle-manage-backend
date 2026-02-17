<?php

namespace App\DTO\Sale\SalePayment;

class CreateSalePaymentDTO
{
    public function __construct(
        public readonly int $sale_id,
        public readonly ?int $exchange_id,
        public readonly int $payment_method_id,
        public readonly ?int $receipt_id,
        public readonly string $receipt_name,
        public readonly ?int $installments,
        public readonly float $value,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['saleID'],
            exchange_id: $data['exchangeID'] ?? null,
            payment_method_id: $data['paymentMethodID'],
            receipt_id: $data['receiptID'] ?? null,
            receipt_name: $data['receiptName'],
            installments: $data['installments'] ?? null,
            value: $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'sale_id' => $this->sale_id,
            'exchange_id' => $this->exchange_id,
            'payment_method_id' => $this->payment_method_id,
            'receipt_id' => $this->receipt_id,
            'receipt_name' => $this->receipt_name,
            'installments' => $this->installments,
            'value' => $this->value,
        ];
    }
}
