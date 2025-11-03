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
        'installments',
        'value',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payment()
    {
        return $this->hasMany(TypeReceipt::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipts::class);
    }
}
