<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Commission extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'commissions';

    protected $fillable = [
        'sale_id',
        'return_id',
        'type',
        'status',
        'product_id',
        'product_name',
        'seller_id',
        'seller_name',
        'seller_email',
        'percentage',
        'commission_value',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "commission:enterprise:{$enterpriseId}",
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

    public function seller()
    {
        return $this->belongsTo(Employee::class);
    }
}
