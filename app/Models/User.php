<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasCacheTags
{
    use HasApiTokens, HasFactory, InvalidatesCache, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'role',
        'enterprise_id',
        'image_id',
        'role_id',
        'department_id',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
            "user:enterprise:{$this->enterprise_id}",
        ];
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if (! $this->role) {
            return false;
        }

        return $this->role->permissions->contains('slug', $permissionSlug);
    }
}
