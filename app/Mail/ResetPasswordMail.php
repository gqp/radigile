<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $resetUrl,
    ) {
    }

    public function build(): static
    {
        return $this->subject('Reset Your Password')
            ->view('emails.reset_password');
    }
}
