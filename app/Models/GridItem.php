<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class GridItem extends Model
{
    use Notifiable;

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

    public function gridGroup()
    {
        return $this->belongsTo(GridGroup::class, 'grid_group_id');
    }
}
