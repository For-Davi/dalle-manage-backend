<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierCatalog extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'supplier_catalog';

    public $incrementing = false;

    protected $primaryKey = ['supplier_id', 'product_variant_id'];

    protected $fillable = [
        'product_variant_id',
        'supplier_id',
        'price',
        'description',
        'enterprise_id',
    ];

    public $timestamps = true;

    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (! is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }

        return $query;
    }

    public function getKey()
    {
        $keys = $this->getKeyName();
        if (! is_array($keys)) {
            return parent::getKey();
        }

        $keyValues = [];
        foreach ($keys as $key) {
            $keyValues[$key] = $this->getAttribute($key);
        }

        return $keyValues;
    }

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function getCacheTags(): array
    {
        if (! $this->enterprise_id) {
            return [];
        }

        return [
            "supplier_catalog:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
