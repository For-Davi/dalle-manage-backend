<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleItem extends Model
{
    use Notifiable;

    protected $table = 'sale_itens';

    protected $fillable = [
        'sale_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_price',
        'quantity',
        'total',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
