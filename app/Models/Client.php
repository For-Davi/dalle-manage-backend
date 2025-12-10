<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

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
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
