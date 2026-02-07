<?php

namespace App\DTO\Exchange\ExchangePayment;

class CreateExchangeAdditionalDTO
{
    public function __construct(
        public readonly int $exchange_id,
        public readonly float $change,
        public readonly float $fees,
        public readonly ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            exchange_id: $data['exchangeID'],
            change: $data['change'],
            fees: $data['fees'],
            description: $data['description'],
        );
    }

    public function toArray(): array
    {
        return [
            'exchange_id' => $this->exchange_id,
            'change' => $this->change,
            'fees' => $this->fees,
            'description' => $this->description,
        ];
    }
}
