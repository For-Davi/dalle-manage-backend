<?php

namespace App\DTO\Transaction\Category;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateTransactionCategoryDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
