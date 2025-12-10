<?php

namespace App\DTO\Receipt\Type;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateTypeReceiptDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
