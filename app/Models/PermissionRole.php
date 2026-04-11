<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PermissionRole extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'permission_role';

    public $timestamps = false;

    protected $fillable = [
        'permission_id',
        'role_id',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "permission_role:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('role')) {
            $this->load('role');
        }

        return $this->role?->enterprise_id;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
