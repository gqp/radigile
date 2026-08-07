<?php

namespace App\Mail;

use App\Models\AssessmentTemplatePublishRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssessmentTemplatePublishRequestRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AssessmentTemplatePublishRequest $publishRequest)
    {
    }

    public function build(): static
    {
        return $this->subject("Update on your template publish request")
            ->view('emails.assessment_template_publish_request_rejected')
            ->with([
                'requesterName' => $this->publishRequest->requester->name,
                'templateTitle' => $this->publishRequest->assessmentTemplate->title,
                'teamUrl' => route('user.teams.show', $this->publishRequest->assessmentTemplate->team_id),
            ]);
    }
}
