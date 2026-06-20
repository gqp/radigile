<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisteredUserInviteNotification extends Mailable
{
use Queueable, SerializesModels;

public $teamName;
public $acceptUrl;

/**
* Create a new message instance.
*/
public function __construct($teamName, $acceptUrl)
{
$this->teamName = $teamName;
$this->acceptUrl = $acceptUrl;
}

/**
* Build the message.
*/
public function build()
{
return $this->subject("You're Invited to Join a Team: {$this->teamName}")
->view('emails.registered_user_invite_notification')
->with([
'teamName' => $this->teamName,
'acceptUrl' => $this->acceptUrl,
]);
}
}
