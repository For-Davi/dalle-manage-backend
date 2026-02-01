<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ExchangePaymentMethod extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'exchange_payments_methods';

    protected $fillable = [
        'exchange_id',
        'receipt_id',
        'receipt_name',
        'payment_method_id',
        'value',
    ];
}
