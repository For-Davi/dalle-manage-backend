<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
    use Notifiable;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'enterprise_id',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }
}
