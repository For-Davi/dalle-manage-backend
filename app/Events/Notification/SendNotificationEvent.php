<?php

namespace App\Events\Notification;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userID;

    public $enterpriseID;

    public function __construct(int $userID, int $enterpriseID)
    {
        $this->userID = $userID;
        $this->enterpriseID = $enterpriseID;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.'.$this->userID);
    }

    public function broadcastAs(): string
    {
        return 'notifications';
    }
}
