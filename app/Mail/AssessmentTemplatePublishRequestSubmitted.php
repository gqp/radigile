<?php

namespace App\Mail;

use App\Models\AssessmentTemplatePublishRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssessmentTemplatePublishRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AssessmentTemplatePublishRequest $publishRequest,
        public string $adminName,
    ) {
    }

    public function build(): static
    {
        return $this->subject("New template publish request: {$this->publishRequest->assessmentTemplate->title}")
            ->view('emails.assessment_template_publish_request_submitted')
            ->with([
                'adminName' => $this->adminName,
                'requesterName' => $this->publishRequest->requester->name,
                'templateTitle' => $this->publishRequest->assessmentTemplate->title,
                'teamName' => $this->publishRequest->assessmentTemplate->team->name,
                'requestMessage' => $this->publishRequest->message,
                'reviewUrl' => route('admin.assessment-templates.index'),
            ]);
    }
}
