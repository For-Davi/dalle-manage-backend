<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingSystem extends Model
{
    use HasFactory;

    protected $table = 'setting_system';

    protected $fillable = [
        'send_notification_stock_critical',
        'enterprise_id',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
