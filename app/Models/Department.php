<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Department extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'parent_id',
        'enterprise_id',
    ];

    public function getCacheTags(): array
    {
        if (! $this->enterprise_id) {
            return [];
        }

        return [
            "department:enterprise:{$this->enterprise_id}",
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }
}
