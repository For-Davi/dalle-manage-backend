<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ReturnItem extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_price',
        'product_color',
        'product_color_name',
        'quantity',
        'total',
        'reason',
        'description',
    ];
}
