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
        public readonly ?string $product_color,
        public readonly ?string $product_color_name,
        public readonly ?string $product_grid_size,
        public readonly ?string $product_grid_name,
        public readonly int $product_code,
        public readonly ?string $product_category,
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
            product_color: $data['productColor'] ?? null,
            product_color_name: $data['productColorName'] ?? null,
            product_grid_size: $data['productGridSize'] ?? null,
            product_grid_name: $data['productGridName'] ?? null,
            product_code: $data['productCode'],
            product_category: $data['productCategory'] ?? null,
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
            'product_color' => $this->product_color,
            'product_color_name' => $this->product_color_name,
            'product_grid_size' => $this->product_grid_size,
            'product_grid_name' => $this->product_grid_name,
            'product_code' => $this->product_code,
            'product_category' => $this->product_category,
            'quantity' => $this->quantity,
            'total' => $this->total,
        ];
    }
}
