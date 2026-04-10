<?php

namespace App\Http\Resources\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $items = $this->return_id !== null ? $this->returnExchangeItems : $this->saleItems;

        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'freight_value' => $this->freight_value,
            'cep' => $this->cep,
            'state' => $this->state,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'address' => $this->address,
            'number_address' => $this->number_address,
            'complement' => $this->complement,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'observation' => $this->observation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'return_id' => $this->return_id,
            'status' => $this->status,
            'scheduled_date' => $this->scheduled_date,
            'delivery_guy_id' => $this->delivery_guy_id,
            'delivery_guy_name' => $this->delivery_guy_name,
            'delivery_guy_phone' => $this->delivery_guy_phone,
            'updated_by_name' => $this->updated_by_name,
            'updated_by_email' => $this->updated_by_email,
            'items' => $items->map(function ($item) {
                return [
                    'product_name'       => $item->product_name,
                    'product_sku'        => $item->product_sku,
                    'product_price'      => $item->product_price,
                    'quantity'           => $item->quantity,
                    'total'              => $item->total,
                    'grid'               => $item->product_grid_name,
                    'product_code'       => $item->product_code,
                    'color'              => $item->product_color,
                    'color_name'         => $item->product_color_name,
                    'quantity_delivered' => $item->quantity_delivered ?? 0,
                    'delivered' => $item->delivered
                ];
            }),
        ];
    }
}