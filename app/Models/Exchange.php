<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Exchange extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'exchanges';

    protected $fillable = [
        'sale_id',
        'return_id',
        'status',
        'exchange_value',
        'difference_value',
        'created_by_name',
        'created_by_email',
        'updated_by_name',
        'updated_by_email',
        'description',
    ];

    public function paymentExchange()
    {
        $this->hasMany(ExchangePaymentMethod::class, 'exchange_id');
    }

    public function paymentDifference()
    {
        $this->hasMany(SalePaymentMethod::class, 'exchange_id');
    }

    public function additional()
    {
        $this->hasOne(ExchangeAdditional::class, 'exchange_id');
    }
}
