<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SaleCancellation extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'sale_cancellations';

    protected $fillable = [
        'sale_id',
        'created_by',
        'created_by_name',
        'created_by_email',
        'reason',
        'description',
    ];

    public function getCacheTags(): array
    {
        $enterpriseId = $this->getEnterpriseID();

        if (! $enterpriseId) {
            return [];
        }

        return [
            "sale_cancellation:enterprise:{$enterpriseId}",
        ];
    }

    protected function getEnterpriseID(): ?int
    {
        if (! $this->relationLoaded('sale')) {
            $this->load('sale');
        }

        return $this->sale?->enterprise_id;
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
