<?php

namespace App\DTO\Department;

class UpdateDepartmentDTO
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

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
