<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrderItem extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'supplier_order_items';

    protected $fillable = [
        'supplier_order_id',
        'product_variant_id',
        'total_cost',
        'unit_cost',
        'quantity_requested',
        'quantity_received',
        'date_received',
        'finished',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "supplier_order_item:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('variant')) {
            $this->load('variant');
        }

        return $this->variant?->enterprise_id;
    }

    public function enterprise()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receivings()
    {
        return $this->hasMany(SupplierOrderReceiving::class, 'supplier_order_item_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
