<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'sex',
        'phone',
        'cpf',
        'cnpj',
        'state_registration',
        'municipal_registration',
        'date_birthday',
        'cep',
        'country',
        'state',
        'city',
        'neighborhood',
        'address',
        'complement',
        'number',
        'enterprise_id',
        'description',
        'credits',
        'credit_expires_at',
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
            "client:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
