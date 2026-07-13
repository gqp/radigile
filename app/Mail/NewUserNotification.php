<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        return (new NewUserWelcomeMail($notifiable->name, $notifiable->email, $this->password, $this->isTestUser))
            ->to($notifiable->email);
    }
}

