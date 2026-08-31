{{-- AI Conclusion Chat --}}
{{-- Usage: @include('components.ai-conclusion', ['context' => [...]]) --}}

@php
    $type = $context['type'] ?? 'general';
    $themeClass = match($type) {
        'physical' => 'physical',
        'mental' => 'mental',
        default => 'other',
    };

    // Build the AI's opening message based on context
    $summary = $context['summary'] ?? 'your concerns';
    $severity = $context['severity'] ?? null;
    $duration = $context['duration'] ?? null;
    $urgencyLabel = $context['urgencyLabel'] ?? 'moderate concern';
    $seekHelp = $context['seekHelp'] ?? [];

    $openingLines = [];
    $openingLines[] = "Thanks for going through the screening. Here's a quick wrap-up of what we covered:";

    if (count($context['symptoms'] ?? []) > 0) {
        $symptomList = implode(', ', array_map(fn($s) => strtolower(str_replace('-', ' ', $s)), $context['symptoms']));
        $openingLines[] = "You reported: {$symptomList}";
    }

    if ($duration) {
        $openingLines[] = "Duration: " . str_replace('-', ' ', $duration);
    }

    if ($severity) {
        $openingLines[] = "Severity: {$severity}/5";
    }

    if ($urgencyLabel) {
        $openingLines[] = "Assessment: <strong>{$urgencyLabel}</strong>";
    }

    if (count($seekHelp) > 0) {
        $openingLines[] = "Recommendation: " . $seekHelp[0];
    }

    $openingLines[] = "Is there anything you'd like to understand better? I can explain what might be going on, suggest questions for your doctor, or help you think through next steps.";

    $geminiConfigured = (new \App\Services\GeminiChatService())->isConfigured();
@endphp

<div class="bg-white rounded-2xl border border-warm-200 shadow-sm overflow-hidden mb-6 animate-fade-in" style="animation-delay: 0.5s;">
    <div class="p-5 border-b border-warm-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-{{ $themeClass }}-300 to-{{ $themeClass }}-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-warm-800">In conclusion</h3>
                <p class="text-xs text-warm-400">
                    @if($geminiConfigured)
                        Ask me anything about your results
                    @else
                        General guidance based on your screening
                    @endif
                </p>
            </div>
            @unless($geminiConfigured)
                <div class="ml-auto">
                    <span class="text-[10px] text-warm-400 bg-warm-100 px-2 py-0.5 rounded-full">Limited mode</span>
                </div>
            @endunless
        </div>
    </div>

    {{-- Chat container --}}
    <div id="ai-chat-{{ $type }}" class="p-5 space-y-4 min-h-[200px] max-h-[500px] overflow-y-auto">
        {{-- AI opening message --}}
        <div class="flex gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-{{ $themeClass }}-300 to-{{ $themeClass }}-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="bg-{{ $themeClass }}-50 border border-{{ $themeClass }}-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <div class="text-sm text-{{ $themeClass }}-700 leading-relaxed space-y-2">
                    @foreach($openingLines as $line)
                        <p>{!! $line !!}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Input area --}}
    <div class="p-4 border-t border-warm-100 bg-warm-50/50">
        <div class="flex items-end gap-2">
            <textarea
                id="ai-input-{{ $type }}"
                rows="1"
                placeholder="{{ $geminiConfigured ? 'Ask a follow-up question...' : 'AI chat not configured — ask admin for API key' }}"
                class="flex-1 px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-sm text-warm-800 placeholder-warm-400 focus:border-{{ $themeClass }}-400 focus:ring-0 resize-none max-h-24 transition-colors"
                {{ $geminiConfigured ? '' : 'disabled' }}
            ></textarea>
            <button
                id="ai-send-{{ $type }}"
                class="w-10 h-10 rounded-xl bg-{{ $themeClass }}-400 hover:bg-{{ $themeClass }}-500 text-white flex items-center justify-center transition-colors shrink-0 disabled:opacity-40"
                disabled
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </div>
        <p class="text-[10px] text-warm-400 mt-2 text-center">This is AI-generated guidance, not medical advice. Always consult a professional for health concerns.</p>
    </div>
</div>

<script>
(function() {
    const type = @json($type);
    const chat = document.getElementById('ai-chat-' + type);
    const input = document.getElementById('ai-input-' + type);
    const sendBtn = document.getElementById('ai-send-' + type);
    const geminiConfigured = @json($geminiConfigured);
    const context = @json($context);
    const themeClass = type === 'physical' ? 'physical' : type === 'mental' ? 'mental' : 'other';

    // Conversation history (sent to API for context)
    let history = [];

    // Fallback responses when API is not configured
    const fallbackResponses = {
        general: {
            default: "I'd recommend discussing this with a healthcare provider who can give you a proper evaluation. Keep track of when symptoms started, what makes them better or worse, and how severe they are — that info will help your doctor.",
            doctor: "Good questions to bring up:\n\n1. \"Could this be related to my symptoms?\"\n2. \"Are there any tests you'd recommend?\"\n3. \"What should I watch for?\"\n4. \"Any lifestyle changes that might help?\"",
            worse: "Seek immediate care if you experience: fever above 103°F (39.4°C), difficulty breathing, chest pain, confusion, or sudden severe symptoms.",
            normal: "It's normal to feel uncertain. The fact that you're looking into it shows you're taking care of yourself. Many alarming symptoms turn out to be manageable, but it's always better to check."
        },
        breathing: {
            default: "Breathing symptoms should be evaluated by a doctor, especially if new or worsening. Avoid irritants like smoke or strong fumes in the meantime.",
            doctor: "Your doctor might recommend: a chest X-ray, pulmonary function tests, allergy assessment, or blood work. Note when symptoms are worst (exercise, rest, night).",
            worse: "Seek immediate care if: severe difficulty breathing, blue lips/fingernails, chest tightness, coughing up blood, or sudden severe breathing difficulty."
        },
        pain: {
            default: "A doctor can help figure out if it's something simple like muscle strain or something needing more investigation.",
            doctor: "Describe: where it hurts, what it feels like (sharp, dull, throbbing), when it started, what makes it better/worse, and rate it 1-10.",
            worse: "Seek immediate care if: sudden severe pain, chest pressure, follows an injury, comes with fever and stiff neck, or you can't move the affected area."
        },
        skin: {
            default: "A dermatologist can usually tell what's going on just by looking at it. Take photos over time to show progression.",
            doctor: "Bring photos of the issue over time, note what makes it better/worse, list any new products/medications, and mention if anyone else has similar symptoms.",
            worse: "See a doctor quickly if: it spreads rapidly, has pus, is very painful, has red streaks, or comes with fever."
        },
        fatigue: {
            default: "Persistent fatigue can have many causes — sleep issues, stress, nutritional deficiencies, thyroid problems. Basic blood work can rule out common causes.",
            doctor: "Your doctor will likely check: sleep habits, thyroid (blood test), iron/vitamin D levels, screen for depression/anxiety. Keep a sleep diary for a week beforehand.",
            worse: "Seek care if fatigue is so severe you can't do daily activities, comes with unexplained weight loss, extreme thirst, or swollen lymph nodes."
        },
        mental: {
            default: "Mental health concerns are just as valid as physical ones. Talking to a professional can help you understand whether this is typical stress, a response to life events, or something that could benefit from treatment.",
            doctor: "Share: how long you've felt this way, what makes it better/worse, how it affects daily life, family history, and any life changes around when it started.",
            worse: "If you're having thoughts of harming yourself, please reach out:\n\n• Crisis Text Line: Text HOME to 741741\n• 988 Suicide & Crisis Lifeline: Call or text 988\n• Emergency: Call 911"
        }
    };

    function getFallbackResponse(text) {
        const lower = text.toLowerCase();
        if (/doctor|appointment|professional|ask|questions|tell.*doctor/i.test(lower)) return findFallback('doctor');
        if (/worse|emergency|urgent|hospital|bad|serious|danger|when.*go|call.*911/i.test(lower)) return findFallback('worse');
        if (/normal|common|everyone|often|is.*it.*bad|should.*i.*worry|anxious|worried/i.test(lower)) return findFallback('normal');
        return findFallback('default');
    }

    function findFallback(key) {
        const ctx = type === 'physical' ? (context.symptoms && context.symptoms.includes('respiratory') ? 'breathing' :
                     context.symptoms && context.symptoms.includes('pain') ? 'pain' :
                     context.symptoms && context.symptoms.includes('skin') ? 'skin' :
                     context.symptoms && context.symptoms.includes('fatigue') ? 'fatigue' : 'general') : type;
        if (fallbackResponses[ctx] && fallbackResponses[ctx][key]) return fallbackResponses[ctx][key];
        return fallbackResponses.general[key] || fallbackResponses.general.default;
    }

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'flex gap-3 justify-end animate-fade-in';
        div.innerHTML = `
            <div class="bg-warm-100 rounded-2xl rounded-tr-md p-4 max-w-[85%]">
                <p class="text-sm text-warm-700 leading-relaxed">${escapeHtml(text)}</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        `;
        chat.appendChild(div);
        div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function addAIMessage(text) {
        const div = document.createElement('div');
        div.className = 'flex gap-3 animate-fade-in';
        const formatted = text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        div.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-${themeClass}-300 to-${themeClass}-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="bg-${themeClass}-50 border border-${themeClass}-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <div class="text-sm text-${themeClass}-700 leading-relaxed space-y-2">${formatted}</div>
            </div>
        `;
        chat.appendChild(div);
        div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function addTypingIndicator() {
        const div = document.createElement('div');
        div.className = 'flex gap-3 animate-fade-in';
        div.id = 'typing-indicator';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-${themeClass}-300 to-${themeClass}-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="bg-${themeClass}-50 border border-${themeClass}-200 rounded-2xl rounded-tl-md px-4 py-3">
                <div class="flex gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-${themeClass}-300 animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-2 h-2 rounded-full bg-${themeClass}-300 animate-bounce" style="animation-delay: 0.15s;"></div>
                    <div class="w-2 h-2 rounded-full bg-${themeClass}-300 animate-bounce" style="animation-delay: 0.3s;"></div>
                </div>
            </div>
        `;
        chat.appendChild(div);
        div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function removeTypingIndicator() {
        const el = document.getElementById('typing-indicator');
        if (el) el.remove();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Auto-resize textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 96) + 'px';
        sendBtn.disabled = input.value.trim().length === 0;
    });

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        addUserMessage(text);
        history.push({ role: 'user', content: text });
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;

        addTypingIndicator();

        if (geminiConfigured) {
            // Call real API
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch('{{ route("api.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: text,
                        history: history.slice(0, -1), // Don't include current message in history
                        context: context,
                    }),
                });

                removeTypingIndicator();
                const data = await res.json();

                if (data.response) {
                    history.push({ role: 'assistant', content: data.response });
                    addAIMessage(data.response);
                } else {
                    // API failed, fall back to canned
                    const fallback = getFallbackResponse(text);
                    history.push({ role: 'assistant', content: fallback });
                    addAIMessage(fallback);
                }
            } catch (err) {
                removeTypingIndicator();
                const fallback = getFallbackResponse(text);
                history.push({ role: 'assistant', content: fallback });
                addAIMessage(fallback);
            }
        } else {
            // Use fallback responses
            setTimeout(() => {
                removeTypingIndicator();
                const response = getFallbackResponse(text);
                history.push({ role: 'assistant', content: response });
                addAIMessage(response);
            }, 800 + Math.random() * 700);
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
})();
</script>
