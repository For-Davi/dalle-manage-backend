<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Receipt extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'receipts';

    protected $fillable = [
        'identifier',
        'type_receipt_id',
        'active',
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

    public function type()
    {
        return $this->belongsTo(TypeReceipt::class, 'type_receipt_id');
    }
}
