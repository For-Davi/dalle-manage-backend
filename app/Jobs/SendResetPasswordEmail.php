<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use App\Models\PasswordResetToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function handle()
    {
        $reset = PasswordResetToken::firstOrNew(['email' => $this->user->email]);

        Mail::to($this->user->email)->send(new ResetPasswordMail($reset->token, $this->user->name, config('app.url')));
    }
}
