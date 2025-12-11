<?php

namespace App\DTO\Receipt;

class FilterReceiptDTO
{
    public function __construct(
        public readonly ?int $active,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            active: $data['active']
        );
    }

    public function toArray(): array
    {
        return [
            'active' => $this->active,
        ];
    }
}
