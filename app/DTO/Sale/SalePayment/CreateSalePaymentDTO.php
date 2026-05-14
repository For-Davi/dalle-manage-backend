<?php

namespace App\DTO\Sale\SalePayment;

class CreateSalePaymentDTO
{
    public function __construct(
        public readonly int $sale_id,
        public readonly int $payment_method_id,
        public readonly ?int $receipt_id,
        public readonly ?string $receipt_name,
        public readonly ?int $installments,
        public readonly float $value,
        public readonly ?int $return_id,
    ) {}

    public static function fromRequest($data, $saleID, $returnID, $receiptName, $paymentMethodID, $installments, $amount): self
    {
        return new self(
            sale_id: $saleID,
            payment_method_id: $paymentMethodID,
            receipt_id: $data['receiptID'] ?? null,
            receipt_name: $receiptName ?? null,
            installments: $installments ?? null,
            value: $amount,
            return_id: $returnID ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'sale_id' => $this->sale_id,
            'payment_method_id' => $this->payment_method_id,
            'receipt_id' => $this->receipt_id,
            'receipt_name' => $this->receipt_name,
            'installments' => $this->installments,
            'value' => $this->value,
            'return_id' => $this->return_id,
        ];
    }
}
