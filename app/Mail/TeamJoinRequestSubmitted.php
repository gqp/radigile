<?php

namespace App\Mail;

use App\Models\TeamJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamJoinRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TeamJoinRequest $joinRequest,
        public string $approverName,
    ) {
    }

    public function build(): static
    {
        return $this->subject("New request to join {$this->joinRequest->team->name}")
            ->view('emails.team_join_request_submitted')
            ->with([
                'approverName' => $this->approverName,
                'requesterName' => $this->joinRequest->user->name,
                'teamName' => $this->joinRequest->team->name,
                'requestMessage' => $this->joinRequest->message,
                'reviewUrl' => route('user.teams.show', $this->joinRequest->team_id),
            ]);
    }
}
