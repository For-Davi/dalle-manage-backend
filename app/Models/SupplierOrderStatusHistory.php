<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SupplierOrderStatusHistory extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'supplier_order_status_history';

    protected $fillable = [
        'supplier_order_id',
        'status',
        'changed_by',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "supplier_order_status_history:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('order')) {
            $this->load('order');
        }

        return $this->order?->enterprise_id;
    }

    public function changed()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function order()
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id');
    }
}
