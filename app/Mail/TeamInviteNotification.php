<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamInviteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $code;      // Invite Code
    public $teamName;  // Name of the team
    public $acceptUrl; // URL to accept the invite

    /**
     * Create a new message instance.
     */
    public function __construct($code, $teamName, $acceptUrl)
    {
        $this->code = $code;
        $this->teamName = $teamName;
        $this->acceptUrl = $acceptUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("You’ve been invited to join the team: {$this->teamName}")
            ->view('emails.team_invite_notification')
            ->with([
                'code' => $this->code,
                'teamName' => $this->teamName,
                'acceptUrl' => $this->acceptUrl,
            ]);
    }
}
