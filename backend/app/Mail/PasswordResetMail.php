<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset your InvoiceHub password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset',
            with: [
                'firstName' => $this->firstName,
                'resetUrl' => $this->resetUrl,
            ],
        );
    }
}
