<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class StockReentryReturnItem extends Model
{
    use HasFactory, Notifiable;

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
}
