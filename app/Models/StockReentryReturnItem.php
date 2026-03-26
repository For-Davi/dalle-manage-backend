<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class StockReentryReturnItem extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'stock_reentries_return_products';

    protected $fillable = [
        'product_variant_id',
        'enterprise_id',
        'product_name',
        'product_sku',
        'product_code',
        'product_color',
        'product_color_name',
        'quantity',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function getCacheTags(): array
    {
        if (! $this->enterprise_id) {
            return [];
        }

        return [
            "stock_reentry_return_item:enterprise:{$this->enterprise_id}",
        ];
    }
}
