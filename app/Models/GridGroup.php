<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class GridGroup extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'grid_groups';

    protected $fillable = [
        'name',
        'active',
        'enterprise_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function items()
    {
        return $this->hasMany(GridItem::class, 'grid_group_id', 'id')->orderBy('order');
    }
}
