<?php

namespace App\DTO\Receipt;

class FilterReceiptDTO
{
    public function __construct(
        public readonly ?int $active,
        public readonly ?int $enterpriseID,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            enterpriseID: $data['enterpriseID'],
            active: $data['active']
        );
    }

    public function toArray(): array
    {
        return [
            'enterpriseID' => $this->enterpriseID,
            'active' => $this->active,
        ];
    }
}
