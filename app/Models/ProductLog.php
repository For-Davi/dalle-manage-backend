<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductLog extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'product_log';

    protected $fillable = [
        'product_id',
        'execution',
        'description',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "product_log:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('product')) {
            $this->load('product');
        }

        return $this->product?->enterprise_id;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
