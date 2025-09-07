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
    ];

    public function enterprise()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
