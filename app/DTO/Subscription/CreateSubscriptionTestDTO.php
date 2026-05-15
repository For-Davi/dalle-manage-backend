<?php

namespace App\DTO\Subscription;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionTestDTO extends BaseDTO
{
    public function __construct(
        public readonly string $expired_date,
        public readonly int $allow_test_free,
        public readonly int $subscription_id,
    ) {}

    public static function fromRequest(): self
    {
        $subscriptionFree = DB::table('subscriptions')
            ->where('name', 'premium')
            ->first();

        return new self(
            allow_test_free: 0,
            expired_date: now()->addDays(7),
            subscription_id: $subscriptionFree->id
        );
    }
}
