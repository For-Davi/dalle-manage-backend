<?php

namespace App\DTO\Department;

use App\DTO\BaseDTO;

class UpdateDepartmentDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?int $parent_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            parent_id: $data['parentId'],
        );
    }
}
