<?php

namespace App\DTO\Grid\Item;

use App\DTO\BaseDTO;

class CreateGridItemDTO extends BaseDTO
{
    public function __construct(
        public readonly string $size,
        public readonly int $order,
        public readonly int $enterprise_id,
        public readonly int $grid_group_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            size: $data['size'],
            order: $data['order'],
            grid_group_id: $data['gridGroupID'],
            enterprise_id: $data['enterpriseID']
        );
    }
}
