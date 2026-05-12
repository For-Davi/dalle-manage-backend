<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Enterprise extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'enterprises';

    protected $fillable = [
        'name',
        'cnpj',
        'cpf',
        'cep',
        'state',
        'city',
        'neighborhood',
        'address',
        'complement',
        'email',
        'phone',
        'subscription_id',
        'number_address',
        'active',
        'seller_id',
        'expired_date',
        'first_payment_subscription',
    ];

    public function setSellerCodeAttribute($value)
    {
        $this->attributes['seller_id'] = strtoupper($value);
    }

    public function getCacheTags(): array
    {
        if (! $this->id) {
            return [];
        }

        return [
            "enterprise:{$this->id}",
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class, 'enterprise_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function typeReceipt()
    {
        return $this->hasMany(TypeReceipt::class, 'enterprise_id', 'id');
    }
}
