<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SalePaymentMethod extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'sale_payments_methods';

    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'receipt_id',
        'receipt_name',
        'installments',
        'value',
        'return_id',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "sale_payment_method:enterprise:{$enterpriseId}",
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

    public function type()
    {
        return $this->belongsTo(TypeReceipt::class, 'payment_method_id');
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
