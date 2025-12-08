<?php

namespace App\Notification;

use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\DB;

class SendNotification
{
    public function notifyAllUsersByEnterprise(int $enterpriseID, string $title, string $message)
    {
        $users = DB::table('users')->where('enterprise_id', $enterpriseID)->get();
        foreach ($users as $user) {
            NotificationHelper::create(
                $user->id,
                $title,
                $message,
                $enterpriseID
            );
        }
    }
}
