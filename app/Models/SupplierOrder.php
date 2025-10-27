<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrder extends Model
{
    use Notifiable;

    protected $table = 'supplier_orders';

    protected $fillable = [
        'supplier_id',
        'status',
        'date_delivery_expected',
        'date_issue',
        'order_number',
        'created_by',
        'cancellation_reason',
        'date_received',
        'description',
        'enterprise_id'
    ];

    public function enterprise()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(SupplierOrderItem::class, 'supplier_order_id');
    }

    public function status()
    {
        return $this->hasMany(SupplierOrderStatusHistory::class, 'supplier_order_id');
    }
}
