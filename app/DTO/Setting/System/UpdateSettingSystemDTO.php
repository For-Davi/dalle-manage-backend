<?php

namespace App\DTO\Setting\System;

use App\DTO\BaseDTO;

class UpdateSettingSystemDTO extends BaseDTO
{
    public function __construct(
        public int $send_notification_stock_critical,
        public readonly int $has_credit_expired_data,
        public readonly int $quantity_credit_expire_days,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            send_notification_stock_critical: $data['sendNotificationStockCritical'],
            has_credit_expired_data: $data['hasCreditExpiredData'],
            quantity_credit_expire_days: $data['quantityCreditExpireDays'],
        );
    }
}
