<?php

namespace App\Jobs\Email;

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

    protected $isSeller;

    public function __construct($user, $token, $isSeller = null)
    {
        $this->user = $user;
        $this->token = $token;
        $this->isSeller = $isSeller;

        $this->onQueue('emails');
    }

    public function handle()
    {
        $reset = PasswordResetToken::firstOrNew(['email' => $this->user->email]);

        Mail::to($this->user->email)->send(new ResetPasswordMail($reset->token, $this->user->name, $this->isSeller ? config('app.url').'/seller' : config('app.url')));
    }
}
