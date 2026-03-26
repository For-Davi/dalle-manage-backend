<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model implements HasCacheTags
{
    use InvalidatesCache, Notifiable;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'read',
        'enterprise_id',
    ];

    public function getCacheTags(): array
    {
        if (! $this->user_id) {
            return [];
        }

        return [
            "notification:user:{$this->user_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
