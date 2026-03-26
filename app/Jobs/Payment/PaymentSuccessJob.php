<?php

namespace App\Jobs\Payment;

use App\Events\Payment\PaymentSuccessEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('payments');
    }

    public function handle(): void
    {
        PaymentSuccessEvent::dispatch();
    }
}
