<?php

namespace App\DTO\Sale;

use Carbon\Carbon;

class CreateSaleDTO
{
    public function __construct(
        public readonly int $enterprise_id,
        public readonly ?int $seller_id,
        public readonly ?string $seller_name,
        public readonly ?int $client_id,
        public readonly ?string $client_name,
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
            seller_name: $data['sellerName'] ?? null,
            client_id: $data['clientID'] ?? null,
            client_name: $data['clientName'] ?? null,
            fees: $data['fees'],
            total: $data['totalValue'],
            change: $data['change'],
            date: Carbon::now('America/Sao_Paulo')->format('Y-m-d H:i:s'),
        );
    }

    public function toArray(): array
    {
        return [
            'enterprise_id' => $this->enterprise_id,
            'seller_id' => $this->seller_id,
            'seller_name' => $this->seller_name,
            'client_id' => $this->client_id,
            'client_name' => $this->client_name,
            'fees' => $this->fees,
            'total' => $this->total,
            'change' => $this->change,
            'date' => $this->date,
        ];
    }
}
