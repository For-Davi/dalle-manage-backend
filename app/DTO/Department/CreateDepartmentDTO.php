<?php

namespace App\DTO\Department;

use Illuminate\Support\Facades\Auth;

class CreateDepartmentDTO
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

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
