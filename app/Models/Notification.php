<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use Notifiable;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'read',
        'enterprise_id',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
