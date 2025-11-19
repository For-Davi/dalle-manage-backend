<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductCategory extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'enterprise_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}
