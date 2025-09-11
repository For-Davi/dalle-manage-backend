<?php

namespace App\DTO\Setting\System;

class CreateSettingSystemDTO
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

    public function toArray(): array
    {
        return [
            'enterprise_id' => $this->enterprise_id,
            'send_notification_stock_critical' => $this->send_notification_stock_critical,
        ];
    }
}
