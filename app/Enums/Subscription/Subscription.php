<?php

namespace App\Enums\Subscription;

enum Subscription: string
{
    case free = 'free';
    case basic = 'basic';
    case premium = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::free => 'GRÁTIS',
            self::basic => 'BÁSICA',
            self::premium => 'PREMIUM',
        };
    }
}
