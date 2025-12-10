<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Base\BaseRepository;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function markAsRead(int $notificationID): ?Notification
    {
        $notification = $this->model
            ->where('id', $notificationID)
            ->first();

        if ($notification) {
            $notification->update(['read' => 1]);

            return $notification;
        }

        return null;
    }

    public function delete(int $notificationID): int
    {
        return $this->model
            ->where('id', $notificationID)
            ->delete();
    }
}
