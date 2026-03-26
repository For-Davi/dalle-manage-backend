<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrder extends Model implements HasCacheTags
{
    use InvalidatesCache,Notifiable;

    protected $table = 'supplier_orders';

    protected $fillable = [
        'supplier_id',
        'status',
        'date_delivery_expected',
        'date_issue',
        'order_number',
        'created_by',
        'cancellation_reason',
        'date_received',
        'observation',
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
            "supplier_order:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(SupplierOrderItem::class, 'supplier_order_id');
    }

    public function status()
    {
        return $this->hasMany(SupplierOrderStatusHistory::class, 'supplier_order_id');
    }
}
