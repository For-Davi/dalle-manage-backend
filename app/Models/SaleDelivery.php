<?php

namespace App\Models;

// use App\Contracts\HasCacheTags;
// use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

// class SaleDelivery extends Model implements HasCacheTags
class SaleDelivery extends Model
{
    // use InvalidatesCache, Notifiable;

    use Notifiable;

    protected $table = 'sale_deliveries';

    protected $fillable = [
        'sale_id',
        'return_id',
        'freight_value',
        'cep',
        'state',
        'city',
        'neighborhood',
        'address',
        'number_address',
        'complement',
        'recipient_name',
        'recipient_phone',
        'observation',
        'status',
        'scheduled_date',
        'delivery_guy_id',
        'delivery_guy_name',
        'delivery_guy_phone',
        'updated_by_name',
        'updated_by_email',
    ];

    // public function getCacheTags(): array
    // {
    //     $enterpriseId = $this->getEnterpriseID();

    //     if (! $enterpriseId) {
    //         return [];
    //     }

    //     return [
    //         "sale_delivery:enterprise:{$enterpriseId}",
    //     ];
    // }

    // protected function getEnterpriseID(): ?int
    // {
    //     if (! $this->relationLoaded('sale')) {
    //         $this->load('sale');
    //     }

    //     return $this->sale?->enterprise_id;
    // }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'sale_id');
    }

    public function returnExchangeItems()
    {
        return $this->hasMany(ReturnExchangeItem::class, 'return_id', 'return_id');
    }
}
