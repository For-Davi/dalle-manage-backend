<?php

namespace App\DTO\Role;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateRoleDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public readonly ?string $description,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
