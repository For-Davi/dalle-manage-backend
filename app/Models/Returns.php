<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Returns extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'returns';

    protected $fillable = [
        'sale_id',
        'linked_return_id',
        'status',
        'exchange_value',
        'difference_value',
        'current_value',
        'fees',
        'change',
        'created_by_name',
        'created_by_email',
        'updated_by_name',
        'updated_by_email',
        'seller_id',
        'seller_name',
        'seller_email',
        'freight_fees',
        'freight_change',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "return:enterprise:{$enterpriseId}",
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
        return $this->belongsTo(Sale::class);
    }

    public function delivery()
    {
        return $this->hasOne(SaleDelivery::class, 'return_id');
    }

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

    public function exchangePaymentMethod()
    {
        return $this->hasMany(ExchangePaymentMethod::class, 'return_id');
    }

    public function salePaymentMethod()
    {
        return $this->hasMany(SalePaymentMethod::class, 'return_id');
    }
}
