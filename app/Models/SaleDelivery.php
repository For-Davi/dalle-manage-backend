<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleDelivery extends Model
{
    use Notifiable;

    protected $table = 'sale_deliveries';

    protected $fillable = [
        'sale_id',
        'exchange_id',
        'freight_value',
        'cep',
        'state',
        'city',
        'neighborhood',
        'address',
        'number_address',
        'complement',
        'recipient_name',
        'recipient_phone',
        'observation',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
