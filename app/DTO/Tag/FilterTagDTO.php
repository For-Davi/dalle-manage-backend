<?php

namespace App\DTO\Tag;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterTagDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?int $active,
        public readonly ?int $enterprise_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] !== '' ? $data['name'] : null,
            active: $data['active'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
