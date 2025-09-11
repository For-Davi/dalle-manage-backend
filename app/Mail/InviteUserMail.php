<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;

    public string $userName;

    public string $userEmail;

    public string $adminName;

    public string $enterpriseName;

    public string $appUrl;

    public function __construct(
        string $token,
        string $userName,
        string $userEmail,
        string $adminName,
        string $enterpriseName,
        string $appUrl
    ) {
        $this->token = $token;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->adminName = $adminName;
        $this->enterpriseName = $enterpriseName;
        $this->appUrl = $appUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite para o Dalle Manage',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invite-user',
            with: [
                'token' => $this->token,
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'adminName' => $this->adminName,
                'enterpriseName' => $this->enterpriseName,
                'appUrl' => $this->appUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
