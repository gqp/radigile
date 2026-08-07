<?php

namespace App\Mail;

use App\Models\AssessmentTemplatePublishRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssessmentTemplatePublishRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AssessmentTemplatePublishRequest $publishRequest)
    {
    }

    public function build(): static
    {
        return $this->subject("Your template \"{$this->publishRequest->assessmentTemplate->title}\" is now public")
            ->view('emails.assessment_template_publish_request_approved')
            ->with([
                'requesterName' => $this->publishRequest->requester->name,
                'templateTitle' => $this->publishRequest->assessmentTemplate->title,
                'teamUrl' => route('user.teams.show', $this->publishRequest->assessmentTemplate->team_id),
            ]);
    }
}
