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

        // Optional richer context from the multi-step flows
        $frequency = $context['frequency'] ?? null;
        $impact = $context['impact'] ?? [];
        $interference = $context['interference'] ?? null;
        $talkedTo = $context['talkedTo'] ?? null;
        $tried = $context['tried'] ?? [];
        $betterWorse = $context['betterWorse'] ?? null;
        $neuroTraits = $context['neurodivergenceTraits'] ?? [];

        $extraLines = [];
        if ($frequency) {
            $extraLines[] = "- Frequency: {$frequency}";
        }
        if (count($impact) > 0) {
            $extraLines[] = '- Affects: ' . implode(', ', array_map(fn($i) => str_replace('-', ' ', $i), $impact));
        }
        if ($interference) {
            $extraLines[] = "- Interference level: {$interference}/5";
        }
        if ($talkedTo) {
            $extraLines[] = '- Has talked to: ' . str_replace('-', ' ', $talkedTo);
        }
        if (count($tried) > 0) {
            $extraLines[] = '- Has tried: ' . implode(', ', array_map(fn($t) => str_replace('-', ' ', $t), $tried));
        }
        if ($betterWorse) {
            $extraLines[] = "- Makes it better/worse: {$betterWorse}";
        }
        if (count($neuroTraits) > 0) {
            $labelMap = [
                'sensory' => 'sensory sensitivities',
                'dopamine' => 'highs and lows around interest / motivation',
                'burnout' => 'burnout from masking or overexerting',
                'sleep' => 'sleep rhythm issues',
                'stims' => 'repetitive movements or habits (stimming)',
                'routines' => 'routines feel non-negotiable',
                'notsure' => 'unsure / want to know more',
            ];
            $traitLabels = implode(', ', array_map(fn($t) => $labelMap[$t] ?? $t, $neuroTraits));
            $extraLines[] = "- User selected possible neurodevelopmental/neurological traits: {$traitLabels}";
        }
        $extraContext = count($extraLines) > 0 ? implode("\n", $extraLines) . "\n" : '';

        $system = "You are a helpful, empathetic health information assistant for a symptom screening app. You are NOT a doctor and do NOT give formal diagnoses. However, you SHOULD provide meaningful, specific, and helpful information. Your role is to help the user understand what they're experiencing, provide detailed health information, explain what various conditions and patterns typically involve, and give practical guidance.\n\n";
        $system .= "IMPORTANT RULES:\n";
        $system .= "1. DO NOT give formal medical diagnoses. Instead say things like: this sounds like it could be consistent with X, this pattern is commonly associated with Y, or many people with similar symptoms find that Z.\n";
        $system .= "2. You SHOULD provide detailed, specific information about what conditions typically involve, what triggers them, how they present, common treatments and coping strategies. Be thorough and educational.\n";
        $system .= "3. For mental health specifically: Explain what anxiety, depression, OCD, social anxiety, burnout, and trauma responses typically feel like. Help the user understand their patterns. Suggest specific coping techniques, grounding exercises, journaling prompts, or behavioral strategies. Explain the difference between normal stress responses and clinical conditions.\n";
        $system .= "4. You can recommend specific self-help strategies, coping techniques, lifestyle changes, and explain how therapy modalities work (CBT, DBT, EMDR, etc.) so the user knows what to expect.\n";
        $system .= "5. If someone describes a crisis (self-harm thoughts, severe emergency), immediately provide crisis resources (988 Suicide & Crisis Lifeline, 741741 Crisis Text Line, or 911) and encourage them to seek immediate help.\n";
        $system .= "6. Be warm, conversational, and non-judgmental. Match the user's tone. If they're frustrated, acknowledge it. If they're confused, help clarify.\n";
        $system .= "7. When the user asks what something might be, give them REAL information — explain possible conditions, what they involve, how they differ, and what patterns to look for. Don't just say 'see a doctor'. Explain WHY and WHAT to tell them.\n";
        $system .= "8. If the user has selected possible neurodevelopmental or neurological traits (e.g. sensory sensitivities, interest/motivation highs and lows, burnout from masking or overexerting, sleep rhythm issues, repetitive movements or habits, routines feeling non-negotiable), treat that as important context. Explain what each selected trait commonly overlaps with in neurodevelopmental conditions (such as autism, ADHD, sensory processing differences, circadian rhythm disorders) AND in non-neurodivergent explanations (anxiety, chronic stress, burnout, depression, sleep deprivation, thyroid issues, etc.). Make it clear that overlap does not mean causation — these things can look similar without being the same.\n";
        $system .= "9. When discussing neurodevelopmental possibilities, use plain, respectful language. Do not diagnose. Do say 'this pattern is commonly seen in people with X' or 'this can be consistent with X' rather than 'you have X'. Avoid armchair diagnosis language. Include both the neurodivergent lens and the non-neurodivergent lens in every answer so the user gets a balanced picture.\n";
        $system .= "10. Always end with: This is general information, not a diagnosis. A professional can give you a proper evaluation.\n";
        $system .= "11. Keep your answers focused and reasonably concise. Use headings (###), bullet points (*), and short paragraphs. Do not write excessively long responses — aim for clarity over length. If you find yourself writing more than about 800 words, stop and wrap up.\n\n";
        $system .= "SCREENING CONTEXT:\n";
        $system .= "- Type: {$type}\n";
        $system .= "- Reported concerns: {$symptomList}\n";
        $system .= "- Duration: {$duration}\n";
        $system .= "- Severity: {$severity}/5\n";
        $system .= "- Assessment: {$urgencyLabel}\n";
        $system .= "{$extraContext}";
        $system .= "{$summaryLine}\n";

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
                    'maxOutputTokens' => 2048,
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
     * Generate content for a specific results section
     */
    public function generateSection(string $section, array $context): ?string
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $type = $context['type'] ?? 'general';
        $symptoms = $context['symptoms'] ?? [];
        $presentSymptoms = $context['presentSymptoms'] ?? [];
        $duration = $context['duration'] ?? null;
        $severity = $context['severity'] ?? null;
        $whyThinking = $context['whyThinking'] ?? ($context['why_thinking'] ?? '');
        $frequency = $context['frequency'] ?? null;
        $impact = $context['impact'] ?? [];
        $interference = $context['interference'] ?? null;
        $tried = $context['tried'] ?? [];
        $talkedTo = $context['talkedTo'] ?? ($context['talked_to'] ?? null);
        $betterWorse = $context['betterWorse'] ?? ($context['better_worse'] ?? null);
        $changes = $context['changes'] ?? [];
        $location = $context['location'] ?? null;

        $symptomList = implode(', ', array_map(fn($s) => str_replace('-', ' ', $s), $symptoms));
        $presentList = count($presentSymptoms) > 0 ? implode(', ', array_map(fn($s) => str_replace('-', ' ', $s), $presentSymptoms)) : 'none reported';
        $impactList = count($impact) > 0 ? implode(', ', array_map(fn($i) => str_replace('-', ' ', $i), $impact)) : 'none reported';
        $triedList = count($tried) > 0 ? implode(', ', array_map(fn($t) => str_replace('-', ' ', $t), $tried)) : 'nothing yet';
        $changesList = count($changes) > 0 ? implode(', ', array_map(fn($c) => str_replace('-', ' ', $c), $changes)) : 'none reported';

        $prompt = $this->buildSectionPrompt($section, $type, $symptomList, $presentList, $duration, $severity, $whyThinking, $frequency, $impactList, $interference, $triedList, $talkedTo, $betterWorse, $changesList, $location);

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $this->buildSectionSystemPrompt()]],
                ],
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topP' => 0.9,
                    'maxOutputTokens' => 2048,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            \Log::warning('Gemini section generation error', [
                'section' => $section,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Gemini section generation exception', ['section' => $section, 'message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * System prompt for section generation
     */
    protected function buildSectionSystemPrompt(): string
    {
        return "You are a health information assistant for a symptom screening app called Tweek. You generate specific, personalized content for individual sections of a screening results page.\n\n" .
        "RULES:\n" .
        "1. NEVER give formal diagnoses. Say 'this pattern is commonly associated with X' or 'this can be consistent with X'.\n" .
        "2. Be specific and personalized — reference the user's actual symptoms, duration, and what they told you. Do NOT give generic advice that could apply to anyone.\n" .
        "3. Use plain, warm, respectful language. Not clinical or robotic.\n" .
        "4. Use markdown formatting: ### for headings, * for bullet points, ** for bold, --- for section dividers.\n" .
        "5. Be concise but thorough. Each section should be 150-400 words depending on complexity.\n" .
        "6. End with: This is general information, not a diagnosis. A professional can give you a proper evaluation.\n";
    }

    /**
     * Build the prompt for a specific section
     */
    protected function buildSectionPrompt(string $section, string $type, string $symptomList, string $presentList, ?string $duration, $severity, string $whyThinking, ?string $frequency, string $impactList, $interference, string $triedList, $talkedTo, $betterWorse, string $changesList, ?string $location = null): string
    {
        $locationLine = $location ? "User's location: {$location}\n" : '';
        $contextBlock = "Type: {$type}\n" .
            "Concerns: {$symptomList}\n" .
            "Additional symptoms: {$presentList}\n" .
            "Duration: {$duration}\n" .
            "Severity: {$severity}/5\n" .
            "Frequency: {$frequency}\n" .
            "Areas affected: {$impactList}\n" .
            "Interference level: {$interference}/5\n" .
            "Things tried: {$triedList}\n" .
            "Talked to: {$talkedTo}\n" .
            "What makes it better/worse: {$betterWorse}\n" .
            "Recent changes: {$changesList}\n" .
            "Why user thinks this: {$whyThinking}\n" .
            $locationLine;

        $prompts = [
            'patterns' => "Based on this screening, explain what this specific pattern of symptoms often involves. Reference the user's actual concerns, duration, and their own description of what they think is going on. Explain the likely mechanisms behind their specific combination of symptoms. Use ### headings for different aspects.\n\n{$contextBlock}",

            'strategies' => "Based on this screening, suggest 4-6 practical, actionable strategies specifically relevant to this user's symptoms and situation. Reference what they've already tried (or haven't tried). Make each strategy concrete with a clear action. Use numbered items.\n\n{$contextBlock}",

            'what-to-tell' => "Write a suggested script for what this user could say when talking to a healthcare professional. Personalize it to their specific symptoms, duration, severity, and the details they shared. Also add 3-4 bullet points of additional things to mention.\n\n{$contextBlock}",

            'correlation' => "Explain how this user's symptoms relate to each other — why they commonly appear together and what the overlap means. Be specific to their combination. Format as a clear explanation with ### for different symptom pairings if helpful.\n\n{$contextBlock}",

            'specialists' => "Based on this user's symptoms, recommend 2-4 SPECIFIC types of specialists or care facilities. Do NOT say 'e.g.' or give generic categories. Give concrete, named facility types that someone could actually search for and find. If the user's location is provided, mention what's commonly available in their area. For each, give a clear, specific name and explain what they do. Use this exact format for each:\n\n### [Specific Facility Type Name]\nOne sentence about what they do and why it fits this user.\n* **What to expect:** Specific details about the visit\n* **Tip:** A concrete actionable tip\n\nExamples of good specific names: 'Community Mental Health Center', 'University Hospital Psychology Department', 'Urgent Care Clinic', 'Behavioral Health Clinic', 'Sleep Study Center', 'Primary Care Physician'. Do NOT use placeholders like [Specialist Name].\n\n{$contextBlock}",

            'all' => "Generate content for ALL of the following sections based on this screening. Separate each section with ===SECTION: [name]===\n\nSections to generate:\n1. patterns — What this pattern often involves\n2. strategies — Practical strategies\n3. what-to-tell — What to tell a professional\n4. correlation — How your symptoms relate to each other\n5. specialists — Recommended support\n\n{$contextBlock}",
        ];

        return $prompts[$section] ?? $prompts['all'];
    }

    /**
     * Region-appropriate helplines for the user's area and concern.
     *
     * Only used when no REAL facility could be found nearby. The model is never
     * asked for facilities here: a card called "care near you" must not name
     * places that only exist in the model's imagination.
     *
     * @return array<int, array{name:string, number:string, note:string}>|null
     */
    public function localHelplines(array $context): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $type = $context['type'] ?? 'general';
        $symptoms = $context['symptoms'] ?? [];
        $presentSymptoms = $context['presentSymptoms'] ?? [];
        $duration = $context['duration'] ?? null;
        $severity = $context['severity'] ?? null;
        $whyThinking = $context['whyThinking'] ?? ($context['why_thinking'] ?? '');
        $location = $context['location'] ?? null;

        $symptomList = implode(', ', array_map(fn($s) => str_replace('-', ' ', $s), $symptoms));
        $presentList = count($presentSymptoms) > 0
            ? implode(', ', array_map(fn($s) => str_replace('-', ' ', $s), $presentSymptoms))
            : 'none reported';

        $locationLine = $location ?: 'unknown (do not guess a city)';

        $prompt = "We could not find a real facility near this person, so give them phone numbers to call instead.\n\n" .
            "User's location: {$locationLine}\n" .
            "Screening type: {$type}\n" .
            "Their main concerns: {$symptomList}\n" .
            "Other symptoms: {$presentList}\n" .
            "Duration: {$duration}\n" .
            "Severity: {$severity}/5\n" .
            "In their own words: {$whyThinking}\n\n" .
            "Return 2-4 helplines: free, always-available numbers that are real and well known for " .
            "this location and concern (crisis lines, nurse advice lines, poison control, domestic " .
            "violence lines, mental health lines, etc), each with a short note about when to use it.\n\n" .
            "Only include a number you are confident is correct and still active. Never invent or guess " .
            "a number, and never invent a facility — if you do not know a real line for this area, " .
            "return an empty helplines list.";

        try {
            $response = Http::timeout(40)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $this->buildNearbyCareSystemPrompt()]],
                ],
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.5,
                    'topP' => 0.9,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => [
                        'type' => 'object',
                        'properties' => [
                            'helplines' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'number' => ['type' => 'string'],
                                        'note' => ['type' => 'string'],
                                    ],
                                    'required' => ['name', 'number', 'note'],
                                ],
                            ],
                        ],
                        'required' => ['helplines'],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!$text) {
                    return null;
                }
                $decoded = json_decode($text, true);
                if (!is_array($decoded)) {
                    return null;
                }
                $helplines = array_values($decoded['helplines'] ?? []);

                return count($helplines) > 0 ? $helplines : null;
            }

            \Log::warning('Gemini local helplines error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Gemini local helplines exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Given a list of REAL nearby facilities, ask the model to explain how each one
     * fits the user's screening, plus return region-appropriate helplines.
     *
     * Returns ['notes' => [['index' => int, 'goodFor' => string, 'why' => string]],
     *          'helplines' => [['name' => string, 'number' => string, 'note' => string]]]
     */
    public function annotatePlaces(array $places, array $context): ?array
    {
        if (empty($this->apiKey) || count($places) === 0) {
            return null;
        }

        $type = $context['type'] ?? 'general';
        $symptoms = $context['symptoms'] ?? [];
        $presentSymptoms = $context['presentSymptoms'] ?? [];
        $duration = $context['duration'] ?? null;
        $severity = $context['severity'] ?? null;
        $whyThinking = $context['whyThinking'] ?? ($context['why_thinking'] ?? '');
        $location = $context['location'] ?? null;

        $symptomList = implode(', ', array_map(fn($s) => str_replace('-', ' ', $s), $symptoms));
        $presentList = count($presentSymptoms) > 0
            ? implode(', ', array_map(fn($s) => str_replace('-', ' ', $s), $presentSymptoms))
            : 'none reported';

        $list = '';
        foreach ($places as $i => $place) {
            $list .= ($i + 1) . '. index=' . $i
                . ' | name: ' . ($place['name'] ?? 'Unnamed')
                . ' | type: ' . ($place['type'] ?? 'unknown')
                . ($place['address'] ? ' | address: ' . $place['address'] : '')
                . ($place['distanceKm'] !== null ? ' | ' . $place['distanceKm'] . ' km away' : '')
                . "\n";
        }

        $prompt = "The user has finished a screening and we found these real facilities near them.\n\n" .
            "User's area: " . ($location ?: 'unknown') . "\n" .
            "Screening type: {$type}\n" .
            "Their main concerns: {$symptomList}\n" .
            "Other symptoms: {$presentList}\n" .
            "Duration: {$duration}\n" .
            "Severity: {$severity}/5\n" .
            "In their own words: {$whyThinking}\n\n" .
            "REAL FACILITIES FOUND:\n{$list}\n" .
            "For EVERY facility above, write:\n" .
            "- goodFor: what this kind of facility is genuinely known for managing, phrased around " .
            "this user's specific concerns (one short sentence, no 'e.g.').\n" .
            "- why: 1-2 sentences on why this specific place suits this person's situation. Reference " .
            "their actual symptoms. Do not claim to know the facility's private details — speak about " .
            "what the facility type is for.\n" .
            "Keep the exact index of each facility. Never rename them or invent extra ones.\n\n" .
            "Then give 2-4 helplines that are real and correct for the user's area and concern " .
            "(crisis lines, nurse advice lines, urgent care lines). Each needs a short note about when to use it.\n\n" .
            "If a facility listing is only loosely relevant to the user's problem, say so honestly in why " .
            "rather than overstating the fit.";

        try {
            $response = Http::timeout(40)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $this->buildNearbyCareSystemPrompt()]],
                ],
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.6,
                    'topP' => 0.9,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => [
                        'type' => 'object',
                        'properties' => [
                            'notes' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'index' => ['type' => 'integer'],
                                        'goodFor' => ['type' => 'string'],
                                        'why' => ['type' => 'string'],
                                    ],
                                    'required' => ['index', 'goodFor', 'why'],
                                ],
                            ],
                            'helplines' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'number' => ['type' => 'string'],
                                        'note' => ['type' => 'string'],
                                    ],
                                    'required' => ['name', 'number', 'note'],
                                ],
                            ],
                        ],
                        'required' => ['notes', 'helplines'],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!$text) {
                    return null;
                }
                $decoded = json_decode($text, true);
                if (!is_array($decoded)) {
                    return null;
                }

                return [
                    'notes' => array_values($decoded['notes'] ?? []),
                    'helplines' => array_values($decoded['helplines'] ?? []),
                ];
            }

            \Log::warning('Gemini place annotation error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Gemini place annotation exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Last-resort helplines when the model cannot be reached.
     * Kept deliberately generic and clearly labelled.
     */
    public function fallbackHelplines(string $type): array
    {
        if ($type === 'mental') {
            return [
                ['name' => '988 Suicide & Crisis Lifeline', 'number' => '988', 'note' => 'US & Canada — free, 24/7, call or text'],
                ['name' => 'Crisis Text Line', 'number' => '741741', 'note' => 'US — text HOME to reach a counselor'],
            ];
        }

        return [
            ['name' => 'Emergency services', 'number' => '112', 'note' => 'Works on most phones worldwide — use for anything urgent'],
        ];
    }

    /**
     * System prompt for nearby care lookups
     */
    protected function buildNearbyCareSystemPrompt(): string
    {
        return "You help people find the right kind of care near them for a symptom screening app called Tweek.\n\n" .
        "RULES:\n" .
        "1. Never invent a specific private practice, clinic, or phone number. Only name facility TYPES " .
        "that reliably exist (e.g. 'Community Mental Health Center', 'University Hospital Sleep Clinic', " .
        "'Urgent Care Clinic'), or a well-known public facility you are confident about.\n" .
        "2. Only include a phone number when it is a widely known public or national line. When unsure, " .
        "leave 'call' empty — the app will offer a map search instead.\n" .
        "3. Always include real helplines for the user's location, because they are the reliable part.\n" .
        "4. Be specific to the user's concern. Someone describing food avoidance should not be sent to " .
        "a general practitioner first — match the specialty to the problem.\n" .
        "5. Never diagnose. Describe what each place is good at managing, not what the user has.\n" .
        "6. Return valid JSON matching the provided schema. No prose outside the JSON.\n";
    }

    /**
     * Check if the API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}