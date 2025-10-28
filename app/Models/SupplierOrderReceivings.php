<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrderReceivings extends Model
{
    use Notifiable;

    protected $table = 'supplier_order_receivings';

    protected $fillable = [
        'supplier_order_item_id',
        'product_variant_id',
        'receiving_date',
        'observation',
        'received_by',
    ];

    public function receivings()
    {
        return $this->hasMany(SupplierOrderItem::class, 'supplier_order_item_id');
    }

    public function received()
    {
        return $this->belongsTo(User::class, 'received_id');
    }
}
