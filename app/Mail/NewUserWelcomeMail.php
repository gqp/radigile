<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public bool $isTestUser = false,
    ) {
    }

    public function build(): static
    {
        return $this->subject($this->isTestUser ? 'Your Test Account Is Ready' : 'Welcome to Radagile')
            ->view('emails.new_user_welcome');
    }
}
