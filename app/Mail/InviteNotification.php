<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $code; // Invite Code
    public $registrationUrl; // Registration link

    /**
     * Create a new message instance.
     */
    public function __construct($code, $registrationUrl)
    {
        $this->code = $code;
        $this->registrationUrl = $registrationUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('You Are Invited to Register')
            ->view('emails.non_registered_invite_join_notification')
            ->with([
                'code' => $this->code,
                'registrationUrl' => $this->registrationUrl,
            ]);
    }
}
