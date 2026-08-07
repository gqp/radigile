<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiQuestionGenerator
{
    /**
     * Generate a single assessment question with a 0-4 maturity rubric via the
     * Anthropic API.
     *
     * @return array{text?: string, tip_0?: string, tip_1?: string, tip_2?: string, tip_3?: string, tip_4?: string, error?: string, status?: int}
     */
    public function generate(string $description, ?string $category = null, ?string $framework = null): array
    {
        $apiKey = config('services.anthropic.api_key');
        $model  = config('services.anthropic.model');

        if (!$apiKey) {
            return ['error' => 'Anthropic API key is not configured.', 'status' => 503];
        }

        $categoryContext  = $category  ? "Category: {$category}" : '';
        $frameworkContext = $framework ? "Framework context: {$framework}" : '';

        $prompt = <<<PROMPT
You are an expert in agile team maturity assessment design. Generate a single high-quality assessment question based on the following:

Competency to assess: {$description}
{$categoryContext}
{$frameworkContext}

The question will be answered by team members rating their own team on a 0–4 maturity scale:
- 0 = Not doing this at all
- 1 = Aware / beginning to explore
- 2 = Developing / inconsistently practicing
- 3 = Competent / consistently practicing
- 4 = Exemplary / mastery, actively improving and coaching others

Return ONLY valid JSON with no markdown or extra text, in this exact format:
{
  "text": "A single clear question a team member can honestly rate their team on (e.g. 'How consistently does our team...' or 'To what degree does our team...')",
  "tip_0": "Concrete 1–2 sentence description of what this looks like at score 0",
  "tip_1": "Concrete 1–2 sentence description of what this looks like at score 1",
  "tip_2": "Concrete 1–2 sentence description of what this looks like at score 2",
  "tip_3": "Concrete 1–2 sentence description of what this looks like at score 3",
  "tip_4": "Concrete 1–2 sentence description of what this looks like at score 4"
}

Guidelines:
- Each tip level must clearly differentiate from adjacent levels
- Tips should describe observable team behaviours, not abstract concepts
- Keep tips concise and specific — avoid vague language like "does well" or "understands"
PROMPT;

        try {
            // Bounds how long this call can hold a PHP-FPM worker: a short connect
            // timeout so an unreachable API fails fast, and a tighter total timeout
            // than the previous 30s so one slow AI call can't tie up a worker for
            // that long — this endpoint runs synchronously in the request cycle.
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->connectTimeout(5)->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $model,
                'max_tokens' => 1024,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (!$response->successful()) {
                \Log::error('Anthropic API error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['error' => 'AI service returned an error. Please try again.', 'status' => 502];
            }

            $content = $response->json('content.0.text');
            $parsed  = json_decode($this->stripJsonFence($content), true);

            if (!$parsed || !isset($parsed['text'], $parsed['tip_0'])) {
                \Log::error('Anthropic response parse failure', ['content' => $content]);
                return ['error' => 'Could not parse AI response. Please try again.', 'status' => 500];
            }

            return $parsed;

        } catch (\Exception $e) {
            \Log::error('Anthropic request failed', ['message' => $e->getMessage()]);
            return ['error' => 'Could not reach AI service. Please try again.', 'status' => 500];
        }
    }

    /**
     * Generate a full set of assessment questions from an assembled team
     * context (see AssessmentContextBuilder::build()), each resolving to
     * either a reused candidate question or a freshly-drafted one.
     *
     * @return array{questions?: array<int, array{source: string, ref?: string, text?: string, category?: string, tips?: array}>, error?: string, status?: int}
     */
    public function generateBatch(array $context, ?string $userNote = null, int $count = 8): array
    {
        $apiKey = config('services.anthropic.api_key');
        $model  = config('services.anthropic.model');

        if (!$apiKey) {
            return ['error' => 'Anthropic API key is not configured.', 'status' => 503];
        }

        $prompt = $this->buildBatchPrompt($context, $userNote, $count);

        try {
            // A batch call does meaningfully more generation work than the
            // single-question one, so it gets more room (tokens) and more
            // time than generate()'s 15s — still synchronous (no queue),
            // per this feature's design: the slow part happens via AJAX
            // from the draft page, not inside the assessment-creation request.
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->connectTimeout(5)->timeout(45)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $model,
                'max_tokens' => 3072,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (!$response->successful()) {
                \Log::error('Anthropic API error (batch)', ['status' => $response->status(), 'body' => $response->body()]);
                return ['error' => 'AI service returned an error. Please try again.', 'status' => 502];
            }

            $content = $response->json('content.0.text');
            $parsed  = json_decode($this->stripJsonFence($content), true);

            if (!$parsed || !isset($parsed['questions']) || !is_array($parsed['questions'])) {
                \Log::error('Anthropic batch response parse failure', ['content' => $content]);
                return ['error' => 'Could not parse AI response. Please try again.', 'status' => 500];
            }

            $questions = $this->normalizeBatch($parsed['questions']);

            // One malformed entry shouldn't waste the whole call, but if
            // most of it came back unusable, treat it as a failure rather
            // than silently handing back far fewer questions than asked for.
            if (count($questions) < max(1, (int) floor($count / 2))) {
                \Log::error('Anthropic batch response mostly malformed', ['content' => $content]);
                return ['error' => 'Could not parse AI response. Please try again.', 'status' => 500];
            }

            return ['questions' => $questions];

        } catch (\Exception $e) {
            \Log::error('Anthropic batch request failed', ['message' => $e->getMessage()]);
            return ['error' => 'Could not reach AI service. Please try again.', 'status' => 500];
        }
    }

    private function buildBatchPrompt(array $context, ?string $userNote, int $count): string
    {
        $team = $context['team'];
        $history = $context['history'];
        $churn = $context['churn'];
        $candidates = $context['candidates'];

        if ($history['has_history']) {
            $weak = $history['weakest_category']
                ? " The weakest area is \"{$history['weakest_category']['name']}\" (scoring {$history['weakest_category']['score']}/4)."
                : '';
            $historyBlock = "Maturity history: overall {$history['overall']}/4 ({$history['overall_band']})"
                . ($history['trend'] ? ", trending {$history['trend']}" : '') . ".{$weak}"
                . ' Prioritize drafting NEW questions that directly probe the weak area, even over reusing candidate questions below.';
        } else {
            $historyBlock = 'This team has no assessment history yet (cold start) — there is no performance '
                . 'signal to calibrate fresh questions against. Prefer selecting strong existing candidate '
                . 'questions over inventing new ones, unless no good candidate match exists for a needed category.';
        }

        $churnBlock = '';
        if (!empty($churn['applicable']) && !empty($churn['is_high_churn'])) {
            $pct = round($churn['ratio'] * 100);
            $churnBlock = "\nMembership churn: {$churn['members_joined_after_last_assessment']} of "
                . "{$churn['total_members']} current members ({$pct}%) joined after the team's last assessment. "
                . 'Lean toward re-establishing foundational/baseline context rather than assuming continuity with prior results.';
        }

        $userNoteBlock = $userNote ? "\nAdditional focus requested by the user: {$userNote}" : '';

        $existingCategoryNames = \App\Models\QuestionCategory::cached()->pluck('name');
        $categoryGuidance = $existingCategoryNames->isNotEmpty()
            ? "Existing categories in the library: " . $existingCategoryNames->implode(', ') . ". "
                . 'For new questions, reuse one of these exactly (same spelling) when the question genuinely fits one; '
                . 'only invent a new short category label when none of the existing ones fit.'
            : '';

        if (!empty($candidates)) {
            $lines = array_map(
                fn ($c) => "[{$c['ref']}] ({$c['category']}) {$c['text']}",
                $candidates
            );
            $candidateBlock = "Candidate questions already used by other teams with the same framework/domain "
                . "(reuse one by reference instead of writing new text where it's a strong fit):\n" . implode("\n", $lines);
        } else {
            $candidateBlock = 'No candidate questions are available from other teams — draft all questions as new.';
        }

        return <<<PROMPT
You are an expert in agile team maturity assessment design. Draft a full set of {$count} assessment questions for the following team, each with a 0–4 maturity rubric.

Team: {$team['name']}
Framework: {$team['framework']}
Domain: {$team['domain']}
Team size: {$team['size']}

{$historyBlock}{$churnBlock}{$userNoteBlock}

{$candidateBlock}

{$categoryGuidance}

Each question will be answered by team members rating their own team on a 0–4 maturity scale:
- 0 = Not doing this at all
- 1 = Aware / beginning to explore
- 2 = Developing / inconsistently practicing
- 3 = Competent / consistently practicing
- 4 = Exemplary / mastery, actively improving and coaching others

Return ONLY valid JSON with no markdown or extra text, in this exact format:
{
  "questions": [
    { "use_candidate": "C3" },
    {
      "new": {
        "text": "A single clear question a team member can honestly rate their team on",
        "category": "A short category label",
        "tips": {
          "0": "Concrete 1-2 sentence description of what this looks like at score 0",
          "1": "...", "2": "...", "3": "...", "4": "..."
        }
      }
    }
  ]
}

Guidelines:
- Return exactly {$count} entries in "questions"
- Never invent a candidate ref that wasn't listed above
- Do not repeat the same underlying competency across two entries, whether candidate or new
- Each tip level must clearly differentiate from adjacent levels; describe observable team behaviours, not abstract concepts
- Prefer diversity across categories where the team's context doesn't point to a specific focus area
PROMPT;
    }

    /**
     * Claude is told to return raw JSON, but sometimes wraps it in a
     * ```json ... ``` markdown fence anyway. Strip that off before decoding
     * rather than failing a response that's otherwise perfectly valid JSON.
     */
    private function stripJsonFence(?string $content): string
    {
        $content = trim((string) $content);
        return preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content);
    }

    private function normalizeBatch(array $rawQuestions): array
    {
        $normalized = [];

        foreach ($rawQuestions as $entry) {
            if (isset($entry['use_candidate']) && is_string($entry['use_candidate'])) {
                $normalized[] = ['source' => 'candidate', 'ref' => $entry['use_candidate']];
                continue;
            }

            $new = $entry['new'] ?? null;
            if (
                is_array($new)
                && !empty($new['text'])
                && !empty($new['category'])
                && isset($new['tips']) && is_array($new['tips'])
                && isset($new['tips']['0'])
            ) {
                $normalized[] = [
                    'source' => 'new',
                    'text' => $new['text'],
                    'category' => $new['category'],
                    'tips' => $new['tips'],
                ];
            }
            // silently skip anything that matches neither shape
        }

        return $normalized;
    }
}
