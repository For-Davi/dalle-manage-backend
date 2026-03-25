<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleDelivery extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'sale_deliveries';

    protected $fillable = [
        'sale_id',
        'exchange_id',
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
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "sale_delivery:enterprise:{$enterpriseId}",
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
}
