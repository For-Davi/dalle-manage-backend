<?php

namespace App\DTO\Grid\Group;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateGridGroupDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['gridName'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
