<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrderStatusHistory extends Model
{
    use Notifiable;

    protected $table = 'supplier_order_status_history';

    protected $fillable = [
        'supplier_order_id',
        'status',
        'changed_by',
    ];

    public function changed()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function order()
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id');
    }
}
