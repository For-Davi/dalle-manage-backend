<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Enterprise extends Model
{
    use Notifiable;

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
    ];

    public function setSellerCodeAttribute($value)
    {
        $this->attributes['seller_id'] = strtoupper($value);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'enterprise_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
