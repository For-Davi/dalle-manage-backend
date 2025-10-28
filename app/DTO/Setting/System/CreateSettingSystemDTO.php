<?php

namespace App\DTO\Setting\System;

use App\DTO\BaseDTO;

class CreateSettingSystemDTO extends BaseDTO
{
    public function __construct(
        public readonly int $enterprise_id,
        public readonly int $send_notification_stock_critical,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            enterprise_id: $data['enterpriseID'],
            send_notification_stock_critical: 1,
        );
    }
}
