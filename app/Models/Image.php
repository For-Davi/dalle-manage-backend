<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Image extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'images';

    protected $fillable = [
        'name',
        'url',
        'enterprise_id',
        'size',
    ];

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
            "image:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
