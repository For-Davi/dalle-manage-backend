<?php

namespace App\DTO\Payment\Pix;

class CreatePixDTO
{
    public function __construct(
        public readonly int $subscriptionID,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            subscriptionID: $data['subscriptionID'],
        );
    }

    public function toArray(): array
    {
        return [
            'subscriptionID' => $this->subscriptionID,
        ];
    }
}
