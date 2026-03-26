<?php

namespace App\Jobs\Email;

use App\Mail\CouponMail;
use App\Mail\ExchangeCouponMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCouponToEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    protected $coupon;

    protected $type;

    public function __construct($email, $coupon, $type = 'sale')
    {
        $this->email = $email;
        $this->coupon = $coupon;
        $this->type = $type;

        $this->onQueue('emails');
    }

    public function handle(): void
    {
        if ($this->type === 'sale') {
            Mail::to($this->email)->send(new CouponMail($this->coupon));
        } else {
            Mail::to($this->email)->send(new ExchangeCouponMail($this->coupon));
        }
    }
}
