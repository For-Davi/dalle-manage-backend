<?php

namespace App\DTO\Sale;

class CreateSaleDTO
{
    public function __construct(
        public readonly int $enterprise_id,
        public readonly ?int $seller_id,
        public readonly ?int $client_id,
        public readonly float $fees,
        public readonly float $total,
        public readonly float $change,
        public readonly string $date,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            enterprise_id: $data['enterpriseID'],
            seller_id: $data['sellerID'] ?? null,
            client_id: $data['clientID'] ?? null,
            fees: $data['fees'],
            total: $data['totalValue'],
            change: $data['change'],
            date: $data['date'],
        );
    }

    public function toArray(): array
    {
        return [
            'enterprise_id' => $this->enterprise_id,
            'seller_id' => $this->seller_id,
            'client_id' => $this->client_id,
            'fees' => $this->fees,
            'total' => $this->total,
            'change' => $this->change,
            'date' => $this->date,
        ];
    }
}
