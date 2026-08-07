<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamRegistrationInvite extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $teamName,
        public string $role,
        public string $registerUrl,
        public string $acceptUrl,
        public bool   $inviteOnly = false,
    ) {}

    public function build(): static
    {
        return $this->subject("You've been invited to join {$this->teamName} on Radagile")
            ->view('emails.team_registration_invite');
    }
}
