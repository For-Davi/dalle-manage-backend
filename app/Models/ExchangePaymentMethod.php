<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ExchangePaymentMethod extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'exchange_payments_methods';

    protected $fillable = [
        'return_id',
        'receipt_id',
        'receipt_name',
        'payment_method_id',
        'value',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "exchange_payment_method:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('type')) {
            $this->load('type');
        }

        return $this->type?->enterprise_id;
    }

    public function type()
    {
        return $this->belongsTo(TypeReceipt::class, 'payment_method_id');
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
