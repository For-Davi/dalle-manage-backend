<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function create(int $userID, string $title, string $message, int $enterpriseID)
    {
        Notification::create([
            'user_id' => $userID,
            'title' => $title,
            'message' => $message,
            'enterprise_id' => $enterpriseID,
        ]);
    }
}
