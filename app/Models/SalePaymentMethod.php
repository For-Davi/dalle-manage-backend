<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SalePaymentMethod extends Model
{
    use Notifiable;

    protected $table = 'sale_payments_methods';

    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'receipt_id',
        'receipt_name',
        'installments',
        'value',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function type()
    {
        return $this->belongsTo(TypeReceipt::class, 'payment_method_id');
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
