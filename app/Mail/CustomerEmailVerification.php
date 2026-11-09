<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verifikasi email Samudera Fresh');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-email-verification',
        );
    }
}
