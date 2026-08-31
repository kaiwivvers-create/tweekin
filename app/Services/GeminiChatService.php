<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class GeminiChatService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = Setting::get('google_api_key', env('GOOGLE_API_KEY', ''));
    }

    /**
     * Build the system prompt based on screening context
     */
    protected function buildSystemPrompt(array $context): string
    {
        $type = $context['type'] ?? 'general';
        $symptoms = $context['symptoms'] ?? [];
        $presentSymptoms = $context['presentSymptoms'] ?? [];
        $duration = $context['duration'] ?? null;
        $severity = $context['severity'] ?? null;
        $urgencyLabel = $context['urgencyLabel'] ?? 'moderate concern';
        $summary = $context['summary'] ?? '';

        $symptomList = implode(', ', array_merge(
            array_map(fn($s) => str_replace('-', ' ', $s), $symptoms),
            $presentSymptoms
        ));

        $system = <<<PROMPT
You are a helpful, empathetic health information assistant for a symptom screening app. You are NOT a doctor and do NOT diagnose. Your role is to help the user understand their symptoms, provide general health information, suggest questions to ask a healthcare provider, and guide them on next steps.

IMPORTANT RULES:
1. NEVER diagnose or say someone "has" a condition. Use phrases like "could be consistent with," "might be worth investigating," or "this pattern sometimes relates to."
2. NEVER prescribe medication or recommend specific treatments beyond general advice.
3. ALWAYS recommend consulting a healthcare professional for proper evaluation.
4. If someone describes a crisis (self-harm thoughts, severe emergency), immediately provide crisis resources (988 Suicide & Crisis Lifeline, 741741 Crisis Text Line, or 911) and encourage them to seek immediate help.
5. Be warm, conversational, and non-judgmental. Match the user's tone.
6. Keep responses concise — 2-4 paragraphs max unless they ask for detail.
7. You can explain what symptoms generally mean, suggest what to tell a doctor, help them track patterns, and offer perspective on when to seek care vs. when to monitor.

SCREENING CONTEXT:
- Type: {$type}
- Reported concerns: {$symptomList}
- Duration: {$duration}
- Severity: {$severity}/5
- Assessment: {$urgencyLabel}
{$summary ? "- Additional notes: {$summary}" : ""}
PROMPT;

        return $system;
    }

    /**
     * Send a message and get a response from Gemini
     */
    public function chat(array $history, string $newMessage, array $context): ?string
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        // Build conversation contents for Gemini
        $contents = [];

        // Add history
        foreach ($history as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]],
            ];
        }

        // Add new message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $newMessage]],
        ];

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topP' => 0.9,
                    'maxOutputTokens' => 1024,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            \Log::warning('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Gemini API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if the API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
