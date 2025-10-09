<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscription extends Model
{
    use Notifiable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'price',
    ];

    public function enterprises()
    {
        return $this->hasMany(Enterprise::class, 'subscription_id');
    }
}
