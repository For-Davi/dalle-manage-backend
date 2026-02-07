<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ExchangeAdditional extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'exchange_additional';

    protected $fillable = [
        'exchange_id',
        'change',
        'fees',
        'description'
    ];
}
