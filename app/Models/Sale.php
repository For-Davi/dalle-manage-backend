<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Sale extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

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
        'starting_total',
        'current_total',
        'change',
        'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function getCacheTags(): array
    {
        if (! $this->enterprise_id) {
            return [];
        }

        return [
            "sale:enterprise:{$this->enterprise_id}",
        ];
    }

    public function delivery()
    {
        return $this->hasOne(SaleDelivery::class, 'sale_id')->where('return_id', null);
    }

    public function payment()
    {
        return $this->hasMany(SalePaymentMethod::class, 'sale_id')
            ->whereNull('return_id')
            ->whereHas('type', function ($query) {
                $query->where('name', '!=', 'CREDIT');
            });
    }

    public function paymentWithCredit()
    {
        return $this->hasMany(SalePaymentMethod::class, 'sale_id')->whereNull('return_id');
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

    public function commission()
    {
        return $this->hasMany(Commission::class, 'sale_id');
    }
}
