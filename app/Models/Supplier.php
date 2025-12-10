<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Supplier extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'cnpj',
        'state_registration',
        'municipal_registration',
        'phone',
        'site',
        'country',
        'state',
        'city',
        'cep',
        'neighborhood',
        'address',
        'number',
        'active',
        'supplier_category_id',
        'enterprise_id',
        'description',
        'complement',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function category()
    {
        return $this->belongsTo(SupplierCategory::class, 'supplier_category_id');
    }
}
