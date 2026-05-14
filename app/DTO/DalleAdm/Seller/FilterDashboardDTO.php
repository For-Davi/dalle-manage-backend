<?php

namespace App\DTO\DalleAdm\Seller;

use App\DTO\BaseDTO;

class FilterDashboardDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $start_period,
        public readonly ?string $end_period,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            start_period: $data['startPeriod'],
            end_period: $data['endPeriod'],
        );
    }

    public function toArray(): array
    {
        return [
            'start_period' => $this->start_period,
            'end_period' => $this->end_period,
        ];
    }
}
