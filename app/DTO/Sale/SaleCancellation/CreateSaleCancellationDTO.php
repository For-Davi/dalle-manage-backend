<?php

namespace App\DTO\Sale\SaleCancellation;

use Illuminate\Support\Facades\Auth;

class CreateSaleCancellationDTO
{
    public function __construct(
        public readonly int $sale_id,
        public readonly int $created_by,
        public readonly string $created_by_name,
        public readonly string $created_by_email,
        public readonly string $reason,
        public readonly ?string $description,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['saleID'],
            created_by: Auth::user()->id,
            created_by_name: Auth::user()->name,
            created_by_email: Auth::user()->email,
            reason: $data['reason'],
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'sale_id' => $this->sale_id,
            'created_by' => $this->created_by,
            'created_by_name' => $this->created_by_name,
            'created_by_email' => $this->created_by_email,
            'reason' => $this->reason,
            'description' => $this->description,
        ];
    }
}
