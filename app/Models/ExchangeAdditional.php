<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ExchangeAdditional extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'exchange_additional';

    protected $fillable = [
        'exchange_id',
        'change',
        'fees',
        'description',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "exchange_additional:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('exchange')) {
            $this->load('exchange.sale');
        }

        return $this->exchange?->sale?->enterprise_id;
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }
}
