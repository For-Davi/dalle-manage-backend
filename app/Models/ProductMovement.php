<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductMovement extends Model
{
    use Notifiable;

    protected $table = 'product_movements';

    protected $fillable = [
        'reason',
        'type',
        'document_number',
        'lot_number',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'total_cost',
        'product_variant_id',
        'supplier_id',
        'created_by',
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

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
