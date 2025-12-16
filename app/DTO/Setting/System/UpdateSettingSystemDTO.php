<?php

namespace App\DTO\Setting\System;

use App\DTO\BaseDTO;

class UpdateSettingSystemDTO extends BaseDTO
{
    public function __construct(
        public int $send_notification_stock_critical,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            send_notification_stock_critical: $data['sendNotificationStockCritical'],
        );
    }
}
