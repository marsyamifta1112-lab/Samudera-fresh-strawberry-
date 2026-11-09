<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerRecoveryCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $recoveryCode,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Kode pemulihan password Samudera Fresh');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-recovery-code',
        );
    }
}
