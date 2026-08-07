<?php

namespace App\Mail;

use App\Models\TeamJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamJoinRequestRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TeamJoinRequest $joinRequest)
    {
    }

    public function build(): static
    {
        return $this->subject("Update on your request to join {$this->joinRequest->team->name}")
            ->view('emails.team_join_request_rejected')
            ->with([
                'requesterName' => $this->joinRequest->user->name,
                'teamName' => $this->joinRequest->team->name,
                'browseUrl' => route('user.teams.browse'),
            ]);
    }
}
