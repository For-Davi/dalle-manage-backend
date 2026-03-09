<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleCancellation extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'sale_cancellations';

    protected $fillable = [
        'sale_id',
        'created_by',
        'created_by_name',
        'created_by_email',
        'reason',
        'description',
    ];
}
