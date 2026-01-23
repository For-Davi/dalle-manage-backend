<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductVariant extends Model
{
    use Notifiable;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_alert',
        'sku',
        'active',
        'grid_item_id',
        'enterprise_id',
        'description',
        'location',
        'color_id',
        'offer',
        'code',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);

        static::created(function ($variant) {
            if (empty($variant->code)) {
                $variant->code = strval($variant->id);
                $variant->saveQuietly();
            }
        });
    }

    public function setSkuAttribute($value)
    {
        if ($value !== null && $value !== '') {
            $this->attributes['sku'] = strtoupper($value);
        } else {
            $this->attributes['sku'] = null;
        }
    }

    public function setCodeAttribute($value)
    {
        if ($value !== null && $value !== '') {
            $this->attributes['code'] = strtoupper($value);
        } else {
            $this->attributes['code'] = null;
        }
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function color()
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function gridItem()
    {
        return $this->belongsTo(GridItem::class, 'grid_item_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_catalog', 'product_variant_id', 'supplier_id');
    }
}
