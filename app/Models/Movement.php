<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Movement extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache, Notifiable;

    protected $table = 'movements';

    protected $fillable = [
        'date',
        'type',
        'value',
        'transaction_category_id',
        'enterprise_id',
        'description',
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
            "movement:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id');
    }
}
