<?php

namespace App\DTO\Supplier\Category;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateSupplierCategoryDTO extends BaseDTO
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
