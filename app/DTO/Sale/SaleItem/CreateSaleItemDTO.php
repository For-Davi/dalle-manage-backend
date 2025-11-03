<?php

namespace App\DTO\Sale\SaleItem;

class CreateSaleItemDTO
{
    public function __construct(
        public readonly int $sale_id,
        public readonly int $product_variant_id,
        public readonly string $product_name,
        public readonly ?string $product_sku,
        public readonly float $product_price,
        public readonly int $quantity,
        public readonly float $total,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['saleID'],
            product_variant_id: $data['productVariantID'],
            product_name: $data['productName'],
            product_sku: $data['productSKU'] ?? null,
            product_price: $data['productPrice'],
            quantity: $data['quantity'],
            total: $data['total'],
        );
    }

    public function toArray(): array
    {
        return [
            'sale_id' => $this->sale_id,
            'product_variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
            'product_price' => $this->product_price,
            'quantity' => $this->quantity,
            'total' => $this->total,
        ];
    }
}
