<?php

namespace App\Mail;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification
{
    private $password;
    private $isTestUser;

    public function __construct(string $password, bool $isTestUser = false)
    {
        $this->password = $password;
        $this->isTestUser = $isTestUser;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->greeting("Hello, {$notifiable->name}!");

        if ($this->isTestUser) {
            $message->line('Your test user account has been created.');
        } else {
            $message->line('Your account has been created. Please verify your email.');
        }

        $message->line('Here are your login credentials:')
            ->line("**Email:** {$notifiable->email}")
            ->line("**Temporary Password:** {$this->password}");

        if (!$this->isTestUser || !$this->password) {
            $message->line('Please log in and change your password.');
        }

        $message->action('Log In', url('/login'))
            ->line('Thank you for using our platform!');

        return $message;
    }
}

