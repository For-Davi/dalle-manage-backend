<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Image extends Model
{
    use Notifiable;

    protected $table = 'images';

    protected $fillable = [
        'name',
        'url',
        'enterprise_id',
        'size',
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
