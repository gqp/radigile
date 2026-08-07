<?php

namespace App\Mail;

use App\Models\TeamJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamJoinRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TeamJoinRequest $joinRequest)
    {
    }

    public function build(): static
    {
        return $this->subject("You're in! Welcome to {$this->joinRequest->team->name}")
            ->view('emails.team_join_request_approved')
            ->with([
                'requesterName' => $this->joinRequest->user->name,
                'teamName' => $this->joinRequest->team->name,
                'teamUrl' => route('user.teams.show', $this->joinRequest->team_id),
            ]);
    }
}
