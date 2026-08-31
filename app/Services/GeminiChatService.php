<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class GeminiChatService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent';

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

        $summaryLine = $summary ? "- Additional notes: {$summary}" : '';

        $system = "You are a helpful, empathetic health information assistant for a symptom screening app. You are NOT a doctor and do NOT give formal diagnoses. However, you SHOULD provide meaningful, specific, and helpful information. Your role is to help the user understand what they're experiencing, provide detailed health information, explain what various conditions and patterns typically involve, and give practical guidance.

";
        $system .= "IMPORTANT RULES:
";
        $system .= "1. DO NOT give formal medical diagnoses. Instead say things like: this sounds like it could be consistent with X, this pattern is commonly associated with Y, or many people with similar symptoms find that Z.
";
        $system .= "2. You SHOULD provide detailed, specific information about what conditions typically involve, what triggers them, how they present, common treatments and coping strategies. Be thorough and educational.
";
        $system .= "3. For mental health specifically: Explain what anxiety, depression, OCD, social anxiety, burnout, and trauma responses typically feel like. Help the user understand their patterns. Suggest specific coping techniques, grounding exercises, journaling prompts, or behavioral strategies. Explain the difference between normal stress responses and clinical conditions.
";
        $system .= "4. You can recommend specific self-help strategies, coping techniques, lifestyle changes, and explain how therapy modalities work (CBT, DBT, EMDR, etc.) so the user knows what to expect.
";
        $system .= "5. If someone describes a crisis (self-harm thoughts, severe emergency), immediately provide crisis resources (988 Suicide & Crisis Lifeline, 741741 Crisis Text Line, or 911) and encourage them to seek immediate help.
";
        $system .= "6. Be warm, conversational, and non-judgmental. Match the user's tone. If they're frustrated, acknowledge it. If they're confused, help clarify.
";
        $system .= "7. When the user asks what something might be, give them REAL information — explain possible conditions, what they involve, how they differ, and what patterns to look for. Don't just say 'see a doctor'. Explain WHY and WHAT to tell them.
";
        $system .= "8. Always end with: This is general information, not a diagnosis. A professional can give you a proper evaluation.

";
        $system .= "SCREENING CONTEXT:
";
        $system .= "- Type: {$type}
";
        $system .= "- Reported concerns: {$symptomList}
";
        $system .= "- Duration: {$duration}
";
        $system .= "- Severity: {$severity}/5
";
        $system .= "- Assessment: {$urgencyLabel}
";
        $system .= "{$summaryLine}
";

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
