<?php

namespace App\DTO\Grid\Item;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class UpdateGridItemDTO extends BaseDTO
{
    public function __construct(
        public string $size,
        public int $active,
        public int $order,
        public readonly int $enterprise_id,
        public readonly int $grid_group_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            size: $data['size'],
            active: $data['active'],
            order: $data['order'],
            grid_group_id: $data['gridGroupID'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
