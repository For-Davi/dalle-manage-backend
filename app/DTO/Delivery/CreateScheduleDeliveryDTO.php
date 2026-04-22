<?php

namespace App\DTO\Delivery;

use App\DTO\BaseDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateScheduleDeliveryDTO extends BaseDTO
{
    public function __construct(
        public string $scheduled_date,
        public ?int $delivery_guy_id,
        public readonly string $status,
        public readonly ?string $delivery_guy_name,
        public readonly ?string $delivery_guy_phone,
        public readonly string $updated_by_name,
        public readonly string $updated_by_email,
    ) {}

    public static function fromRequest($data, $deliveryGuyName, $deliveryGuyPhone): self
    {
        return new self(
            scheduled_date: Carbon::createFromFormat('d/m/Y', $data->schedule)->format('Y-m-d'),
            status: 'scheduled',
            delivery_guy_id: $data->deliveryGuyID,
            delivery_guy_name: $deliveryGuyName ?? null,
            delivery_guy_phone: $deliveryGuyPhone ?? null,
            updated_by_name: Auth::user()->name,
            updated_by_email: Auth::user()->email
        );
    }
}
