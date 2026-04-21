<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DeliveryGuy extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'delivery_guys';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf',
        'vehicle',
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
            "delivery_guy:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
