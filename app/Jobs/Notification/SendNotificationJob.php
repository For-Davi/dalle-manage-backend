<?php

namespace App\Jobs\Notification;

use App\Events\Notification\SendNotificationEvent;
use App\Helpers\NotificationHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;

    protected $message;

    protected $userID;

    protected $enterpriseID;

    protected $onlyUser;

    public function __construct(string $title, string $message, ?int $userID = null, ?int $enterpriseID = null, bool $onlyUser = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->userID = $userID;
        $this->enterpriseID = $enterpriseID;
        $this->onlyUser = $onlyUser;

        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        if ($this->onlyUser && $this->userID) {
            $user = User::find($this->userID);

            NotificationHelper::create(
                userID: $this->userID,
                title: $this->title,
                message: $this->message,
                enterpriseID: $user->enterprise_id
            );

            SendNotificationEvent::dispatch($this->userID, $user->enterprise_id);

        } elseif ($this->enterpriseID) {
            User::where('enterprise_id', $this->enterpriseID)
                ->get(['id'])
                ->each(function ($user) {
                    NotificationHelper::create(
                        userID: $user->id,
                        title: $this->title,
                        message: $this->message,
                        enterpriseID: $this->enterpriseID
                    );

                    SendNotificationEvent::dispatch($user->id, $this->enterpriseID);
                });
        }
    }
}
