<?php

namespace App\Jobs;

use App\Mail\InviteUserMail;
use App\Models\PasswordResetToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendInviteUserEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $admin;

    private $enterprise;

    private $token;

    public function __construct($user, $admin, $enterprise, $token)
    {
        $this->user = $user;
        $this->admin = $admin;
        $this->enterprise = $enterprise;
        $this->token = $token;
    }

    public function handle(): void
    {
        $reset = PasswordResetToken::firstOrNew(['email' => $this->user->email]);

        Mail::to($this->user->email)->send(new InviteUserMail(
            $reset->token,
            $this->user->name,
            $this->user->email,
            $this->admin->name,
            $this->enterprise->name,
            config('app.url')));
    }
}
