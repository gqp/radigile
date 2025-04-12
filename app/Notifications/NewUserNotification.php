<?php

namespace App\Notifications;

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
        return (new MailMessage)
            ->greeting("Hello, {$notifiable->name}!")
            ->line($this->isTestUser
                ? 'Your test user account has been created.'
                : 'Your account has been created.'
            )
            ->line('Here are your login credentials:')
            ->line("**Email:** {$notifiable->email}")
            ->line("**Temporary Password:** {$this->password}")
            ->line('Please log in and change your password.')
            ->action('Log In', url('/login'))
            ->line('Thank you for using our platform!');
    }
}
