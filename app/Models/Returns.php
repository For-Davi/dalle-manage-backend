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
        'linked_return_id',
        'status',
        'created_by_name',
        'created_by_email',
        'updated_by_name',
        'updated_by_email',
    ];

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function returnExchangeItems()
    {
        return $this->hasMany(ReturnExchangeItem::class, 'return_id');
    }

    public function returns()
    {
        return $this->hasOne(Returns::class, 'linked_return_id');
    }

    public function exchanges()
    {
        return $this->hasOne(Exchange::class, 'return_id');
    }
}
