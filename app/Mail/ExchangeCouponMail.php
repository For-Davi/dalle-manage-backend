<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExchangeCouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public $coupon;

    public $formattedDate;

    public function __construct($coupon)
    {
        $this->coupon = $coupon;

        $this->formattedDate = Carbon::parse($coupon['exchange']['created_at'])
            ->format('d/m/Y H:i:s');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cupom da venda {$this->formattedDate}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.exchange-tax-coupon',
            with: [
                'coupon' => $this->coupon,
                'formattedDate' => $this->formattedDate,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
