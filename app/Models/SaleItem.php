<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleItem extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'sale_itens';

    protected $fillable = [
        'sale_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_price',
        'product_color',
        'product_color_name',
        'product_grid_size',
        'product_grid_name',
        'product_code',
        'product_category',
        'quantity',
        'total',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "sale_item:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('sale')) {
            $this->load('sale');
        }

        return $this->sale?->enterprise_id;
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
