<?php

namespace App\DTO\Department;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateDepartmentDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $parent_id,
        public string $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            parent_id: $data['parentId'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
