<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use Notifiable;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'email',
        'sex',
        'cpf',
        'cnpj',
        'date_birthday',
        'state_registration',
        'municipal_registration',
        'phone',
        'country',
        'state',
        'city',
        'cep',
        'neighborhood',
        'address',
        'number',
        'active',
        'complement',
        'has_login_access',
        'active',
        'description',
        'enterprise_id',
        'department_id',
        'user_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
