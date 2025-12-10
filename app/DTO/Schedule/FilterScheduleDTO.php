<?php

namespace App\DTO\Schedule;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class FilterScheduleDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $period,
        public readonly string $type,
        public readonly ?int $category,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            period: $data['period'],
            type: $data['type'],
            category: $data['category'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
