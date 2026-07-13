<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $verificationUrl,
    ) {
    }

    public function build(): static
    {
        return $this->subject('Verify Your Email Address')
            ->view('emails.verify_email');
    }
}
