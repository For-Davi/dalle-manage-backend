<?php

namespace App\DTO\Setting\System;

class UpdateSettingSystemDTO
{
    public function __construct(
        public int $send_notification_stock_critical,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            send_notification_stock_critical: $data['sendNotificationStockCritical'],
        );
    }

    public function toArray(): array
    {
        return [
            'send_notification_stock_critical' => $this->send_notification_stock_critical,
        ];
    }
}
