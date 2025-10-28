<?php

namespace App\DTO\Enterprise;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\DB;

class EnterpriseStartDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $seller_id,
        public int $subscription_id
    ) {}

    public static function fromRequest($data): self
    {
        $subscription = DB::table('subscriptions')
            ->where('name', 'free')
            ->first();

        return new self(
            name: $data['nameEnterprise'],
            seller_id: $data['sellerCode'] ?? null,
            subscription_id: $subscription->id
        );
    }
}
