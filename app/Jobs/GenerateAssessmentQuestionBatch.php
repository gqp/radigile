<?php

namespace App\Jobs;

use App\Models\Assessment;
use App\Services\AiQuestionGenerator;
use App\Services\AssessmentContextBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Runs the actual Claude call for "Generate Full Assessment" on the queue
 * worker rather than inline in the request — a full batch (8+ questions)
 * routinely takes 20-30+ seconds, which is too long to hold open a
 * synchronous web request reliably. The controller dispatches this and
 * immediately returns a request id; the browser polls
 * AssessmentController::pollGenerateBatch() until this job writes a result.
 */
class GenerateAssessmentQuestionBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        private int $assessmentId,
        private string $requestId,
        private ?string $note,
        private int $count,
    ) {
    }

    public function handle(AiQuestionGenerator $generator, AssessmentContextBuilder $contextBuilder): void
    {
        $assessment = Assessment::find($this->assessmentId);

        if (!$assessment) {
            $this->storeResult(['status' => 'failed', 'error' => 'Assessment no longer exists.']);
            return;
        }

        $context = $contextBuilder->build($assessment->team);
        $result = $generator->generateBatch($context, $this->note, $this->count);

        if (isset($result['error'])) {
            $this->storeResult(['status' => 'failed', 'error' => $result['error']]);
            return;
        }

        // Resolve candidate refs back to real Question rows the context
        // builder already loaded — never trust the model to echo a real id.
        $candidatesByRef = collect($context['candidates'])->keyBy('ref');

        $drafts = collect($result['questions'])->map(function ($q) use ($candidatesByRef) {
            if ($q['source'] === 'candidate' && $candidate = $candidatesByRef->get($q['ref'] ?? null)) {
                return [
                    'source'      => 'candidate',
                    'question_id' => $candidate['id'],
                    'text'        => $candidate['text'],
                    'category'    => $candidate['category'],
                    'tips'        => $candidate['tips'],
                ];
            }

            return [
                'source'   => 'new',
                'text'     => $q['text'] ?? '',
                'category' => $q['category'] ?? '',
                'tips'     => $q['tips'] ?? [],
            ];
        })->filter(fn ($d) => $d['source'] === 'candidate' || $d['text'] !== '')->values();

        $this->storeResult(['status' => 'completed', 'drafts' => $drafts]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('GenerateAssessmentQuestionBatch failed', ['message' => $e->getMessage()]);
        $this->storeResult(['status' => 'failed', 'error' => 'Something went wrong generating questions. Please try again.']);
    }

    private function storeResult(array $result): void
    {
        Cache::put(
            "ai-batch-request:{$this->requestId}",
            ['assessment_id' => $this->assessmentId, ...$result],
            now()->addMinutes(10)
        );
    }
}
