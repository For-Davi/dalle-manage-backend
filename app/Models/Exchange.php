<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Exchange extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'exchanges';

    protected $fillable = [
        'sale_id',
        'return_id',
        'status',
        'exchange_value',
        'difference_value',
        'created_by_name',
        'created_by_email',
        'updated_by_name',
        'updated_by_email',
        'description',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "exchange:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('sale')) {
            $this->load('sale');
        }

        return $this->sale?->enterprise_id;
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function return()
    {
        return $this->belongsTo(Returns::class, 'return_id');
    }

    public function paymentExchange()
    {
        return $this->hasMany(ExchangePaymentMethod::class, 'exchange_id');
    }

    public function paymentDifference()
    {
        return $this->hasMany(SalePaymentMethod::class, 'exchange_id');
    }

    public function additionalExchange()
    {
        return $this->hasOne(ExchangeAdditional::class, 'exchange_id');
    }

    public function delivery()
    {
        return $this->hasOne(SaleDelivery::class, 'exchange_id');
    }
}
