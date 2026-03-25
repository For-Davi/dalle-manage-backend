<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrderReceiving extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'supplier_order_receivings';

    protected $fillable = [
        'supplier_order_item_id',
        'product_variant_id',
        'receiving_date',
        'quantity_received',
        'quantity_stocked',
        'received_by',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "supplier_order_receiving:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('variant')) {
            $this->load('variant');
        }

        return $this->variant?->enterprise_id;
    }

    public function receivings()
    {
        return $this->hasMany(SupplierOrderItem::class, 'supplier_order_item_id');
    }

    public function received()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
