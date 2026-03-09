<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Commission extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'commissions';

    protected $fillable = [
        'sale_id',
        'return_id',
        'type',
        'status',
        'product_id',
        'product_name',
        'seller_id',
        'seller_name',
        'seller_email',
        'percentage',
        'commission_value',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function seller()
    {
        return $this->belongsTo(Employee::class);
    }
}
