<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Sale extends Model
{
    use Notifiable;

    protected $table = 'sales';

    protected $fillable = [
        'enterprise_id',
        'seller_id',
        'client_id',
        'fees',
        'total',
        'change',
        'date',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function seller()
    {
        return $this->belongsTo(Employee::class);
    }
}
