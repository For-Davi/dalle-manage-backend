<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ReturnExchangeItem extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'return_exchange_items';

    protected $fillable = [
        'return_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_code',
        'product_price',
        'product_color',
        'product_color_name',
        'product_grid_size',
        'product_grid_name',
        'quantity',
        'delivered',
        'quantity_delivered',
        'total',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "return_exchange_item:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('variant')) {
            $this->load('variant');
        }

        return $this->variant?->enterprise_id;
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
