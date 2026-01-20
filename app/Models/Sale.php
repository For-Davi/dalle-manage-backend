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
        'status',
        'seller_id',
        'seller_name',
        'client_id',
        'client_name',
        'fees',
        'total',
        'change',
        'date',
    ];

    public function delivery()
    {
        return $this->hasOne(SaleDelivery::class, 'sale_id');
    }

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
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'sale_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }
}
