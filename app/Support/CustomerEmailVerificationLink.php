<?php

namespace App\Support;

use App\Mail\CustomerEmailVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CustomerEmailVerificationLink
{
    public static function send(int $customerId, string $customerName, string $email): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'pelanggan.email.verify',
            now()->addMinutes(60),
            [
                'id' => $customerId,
                'hash' => sha1($email),
            ]
        );

        Mail::to($email)->send(
            new CustomerEmailVerification($customerName, $verificationUrl)
        );
    }
}
