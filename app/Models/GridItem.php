<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class GridItem extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'grid_items';

    protected $fillable = [
        'size',
        'active',
        'order',
        'grid_group_id',
        'enterprise_id',
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
            "grid_item:enterprise:{$this->enterprise_id}",
        ];
    }

    public function gridGroup()
    {
        return $this->belongsTo(GridGroup::class, 'grid_group_id');
    }
}
