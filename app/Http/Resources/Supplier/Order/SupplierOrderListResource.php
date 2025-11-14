<?php

namespace App\Http\Resources\Supplier\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierOrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'date_issue' => $this->date_issue,
            'date_delivery_expected' => $this->date_delivery_expected,
        ];
    }
}
