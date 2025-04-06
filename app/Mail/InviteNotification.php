<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $code; // Invite Code

    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('You Are Invited to Register')
            ->view('emails.invite_notification')
            ->with(['code' => $this->code]);
    }
}
