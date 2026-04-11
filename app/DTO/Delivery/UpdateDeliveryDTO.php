<?php

namespace App\DTO\Delivery;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class UpdateDeliveryDTO extends BaseDTO
{
    public function __construct(
        public readonly string $status,
        public readonly ?int $delivery_guy_id,
        public readonly ?string $delivery_guy_name,
        public readonly ?string $delivery_guy_phone,
        public readonly string $updated_by_name,
        public readonly string $updated_by_email,
    ) {}

    public static function fromRequest($status, $deliveryGuy = null): self
    {
        return new self(
            status: $status,
            delivery_guy_id: $deliveryGuy ? $deliveryGuy->id : null,
            delivery_guy_name: $deliveryGuy ? $deliveryGuy->name : null,
            delivery_guy_phone: $deliveryGuy ? $deliveryGuy->phone : null,
            updated_by_name: Auth::user()->name,
            updated_by_email: Auth::user()->email
        );
    }
}
