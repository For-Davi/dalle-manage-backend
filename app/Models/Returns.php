<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Returns extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'returns';

    protected $fillable = [
        'sale_id',
        'status',
        'created_by_name',
        'created_by_email',
        'updated_by_name',
        'updated_by_email',
    ];
}
