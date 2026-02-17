<?php

namespace App\DTO\Return;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateReturnDTO extends BaseDTO
{
    public function __construct(
        public int $sale_id,
        public ?int $linked_return_id,
        public string $status,
        public string $created_by_name,
        public string $created_by_email,
        public ?string $updated_by_name,
        public ?string $updated_by_email,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            sale_id: $data['saleID'],
            linked_return_id: $data['returnID'] ?? null,
            status: 'active',
            created_by_name: Auth::user()->name,
            created_by_email: Auth::user()->email,
            updated_by_name: null,
            updated_by_email: null,
        );
    }
}
