<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrderItem extends Model
{
    use Notifiable;

    protected $table = 'supplier_orders';

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
        return $this->hasMany(SupplierOrderReceivings::class, 'supplier_order_item_id');
    }
}
