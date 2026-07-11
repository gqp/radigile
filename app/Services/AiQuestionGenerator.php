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
            $parsed  = json_decode($content, true);

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
}
