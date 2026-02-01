<?php

namespace App\DTO\Exchange\ExchangePayment;

class CreateExchangeChangeDTO
{
    public function __construct(
        public readonly int $exchange_id,
        public readonly int $change,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            exchange_id: $data['exchangeID'],
            change: $data['change'],
        );
    }

    public function toArray(): array
    {
        return [
            'exchange_id' => $this->exchange_id,
            'change' => $this->change,
        ];
    }
}
