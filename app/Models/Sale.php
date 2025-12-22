<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Sale extends Model
{
    use Notifiable;

    protected $table = 'sales';

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $fillable = [
        'enterprise_id',
        'seller_id',
        'client_id',
        'fees',
        'total',
        'change',
        'date',
    ];

    public function payment()
    {
        return $this->hasMany(SalePaymentMethod::class, 'sale_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

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

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }
}
