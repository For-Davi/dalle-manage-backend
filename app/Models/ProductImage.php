<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductImage extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'product_image';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_id',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "product_image:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('product')) {
            $this->load('product');
        }

        return $this->product?->enterprise_id;
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
