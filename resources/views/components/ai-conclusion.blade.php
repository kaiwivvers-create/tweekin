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

    // Rich fallback responses when API is not configured or fails
    const fallbackResponses = {
        general: {
            default: "I'd recommend discussing this with a healthcare provider who can give you a proper evaluation. Keep track of when symptoms started, what makes them better or worse, and how severe they are — that info will help your doctor.",
            doctor: "Good questions to bring up:\n\n1. \"Could this be related to my symptoms?\"\n2. \"Are there any tests you'd recommend?\"\n3. \"What should I watch for?\"\n4. \"Any lifestyle changes that might help?\"",
            worse: "Seek immediate care if you experience: fever above 103°F (39.4°C), difficulty breathing, chest pain, confusion, or sudden severe symptoms.",
            understand: "Based on what you described, here are some things to consider:\n\n• The duration matters — symptoms lasting weeks or months suggest something ongoing rather than a passing issue\n• Track your patterns: when do they start, what makes them better or worse, and how severe do they get?\n• Common causes often include stress, sleep issues, nutritional gaps, or underlying conditions that are very treatable once identified\n• Keeping a brief daily log (even just ratings on a 1-10 scale) can reveal patterns you might miss\n\nThis is general information, not a diagnosis. A professional evaluation can help narrow things down."
        },
        breathing: {
            default: "Breathing symptoms should be evaluated by a doctor, especially if new or worsening. Common causes include asthma, allergies, anxiety (which can cause real physical breathing difficulty), infections, or environmental irritants.\n\nIn the meantime: avoid smoke and strong fumes, try slow breathing exercises (4 counts in, 7 hold, 8 out), and note when symptoms are worst.",
            doctor: "Your doctor might recommend: a chest X-ray, pulmonary function tests, allergy assessment, or blood work.\n\nTell them: when it started, what makes it worse (exercise, cold air, lying down, stress), whether it comes with wheezing or chest tightness, and if you've had any recent illnesses.",
            worse: "Seek immediate care if: severe difficulty breathing, blue lips/fingernails, chest tightness, coughing up blood, or sudden severe breathing difficulty.",
            understand: "Breathing issues can stem from several sources:\n\n• **Respiratory**: asthma, COPD, infections (pneumonia, bronchitis), allergies\n• **Cardiac**: heart conditions can sometimes present as breathlessness\n• **Anxiety**: hyperventilation and air hunger are very real physical symptoms of anxiety — your body enters fight-or-flight and your breathing pattern changes\n• **Environmental**: altitude, pollution, dry air, allergens\n\nA key question: does it happen at rest or only with exertion? Does it come with wheezing, chest pain, or a feeling of tightness? This helps narrow it down.\n\nThis is general information, not a diagnosis."
        },
        pain: {
            default: "A doctor can help figure out if it's something simple like muscle strain or something needing more investigation.\n\nIn the meantime: note where the pain is, what type it is (sharp, dull, throbbing), when it started, and what makes it better or worse. This info is gold for your doctor.",
            doctor: "Describe: where it hurts, what it feels like (sharp, dull, throbbing, burning), when it started, what makes it better/worse, and rate it 1-10.\n\nAlso mention: does it radiate? Is it constant or does it come and go? Any swelling, redness, or warmth? Did anything trigger it?",
            worse: "Seek immediate care if: sudden severe pain, chest pressure, follows an injury, comes with fever and stiff neck, or you can't move the affected area.",
            understand: "Pain types and what they might suggest:\n\n• **Sharp/stabbing**: often nerve-related or inflammation (e.g., pinched nerve, tendonitis)\n• **Dull/aching**: often muscular or deeper tissue (e.g., strain, arthritis)\n• **Throbbing**: often vascular or inflammatory (e.g., migraine, infection)\n• **Burning**: often nerve-related (e.g., nerve damage, acid reflux)\n\nLocation matters too:\n• Head pain + light sensitivity → could be migraine\n• Joint pain + morning stiffness → could be arthritis\n• Back pain + leg tingling → could be nerve-related\n\nThis is general information, not a diagnosis."
        },
        skin: {
            default: "A dermatologist can usually tell what's going on just by looking at it. Take photos over time to show progression.\n\nCommon skin issues include: eczema, psoriasis, contact dermatitis (allergic reaction), fungal infections, or acne. Many are very treatable once properly identified.",
            doctor: "Bring photos of the issue over time, note what makes it better/worse, list any new products/medications, and mention if anyone else has similar symptoms.",
            worse: "See a doctor quickly if: it spreads rapidly, has pus, is very painful, has red streaks, or comes with fever.",
            understand: "Skin issues often fall into these categories:\n\n• **Allergic/contact**: red, itchy, raised — often from a new product, plant, or material\n• **Fungal**: ring-shaped, scaly edges, can spread — common in warm, moist areas\n• **Inflammatory**: eczema (dry, itchy patches), psoriasis (thick, silvery scales)\n• **Infection**: painful, swollen, warm, possibly with pus\n• **Hormonal**: acne along jaw/chin, back, or chest\n\nA helpful trick: take a photo each week to track changes. This helps your doctor see the pattern.\n\nThis is general information, not a diagnosis."
        },
        fatigue: {
            default: "Persistent fatigue can have many causes — sleep issues, stress, nutritional deficiencies, thyroid problems, or mood disorders. Basic blood work can rule out common causes.\n\nTry: consistent sleep schedule, reducing caffeine after noon, staying hydrated, and light exercise. If it persists beyond 2 weeks, see a doctor.",
            doctor: "Your doctor will likely check: sleep habits, thyroid (blood test), iron/vitamin D levels, and screen for depression/anxiety.\n\nBefore your appointment: keep a sleep diary for a week, note your energy levels throughout the day, and list any recent life changes.",
            worse: "Seek care if fatigue is so severe you can't do daily activities, comes with unexplained weight loss, extreme thirst, or swollen lymph nodes.",
            understand: "Fatigue is one of the most common symptoms and has many possible causes:\n\n• **Sleep**: poor sleep quality, sleep apnea, irregular schedule\n• **Nutritional**: iron deficiency, vitamin D deficiency, B12 deficiency, dehydration\n• **Hormonal**: thyroid issues, diabetes, hormonal imbalances\n• **Mental health**: depression, anxiety, and burnout all cause physical exhaustion\n• **Medical**: anemia, chronic infections, autoimmune conditions\n\nA key distinction: is it physical tiredness (body feels heavy) or mental exhaustion (brain feels foggy)? Or both? This helps narrow the cause.\n\nThis is general information, not a diagnosis."
        },
        mental: {
            default: "Based on what you shared, there are several things that could be happening — and most of them are very treatable. Let me break it down based on your specific concerns.",
            doctor: "Share with a professional: how long you've felt this way, what makes it better/worse, how it affects daily life, family history, and any life changes around when it started.\n\nA therapist will likely ask about: sleep, appetite, energy, social interactions, and any triggers. Writing these down beforehand helps you remember everything.",
            worse: "If you're having thoughts of harming yourself, please reach out:\n\n• Crisis Text Line: Text HOME to 741741\n• 988 Suicide & Crisis Lifeline: Call or text 988\n• Emergency: Call 911\n\nYou matter, and help is available right now.",
            understand: "Based on what you described, here's what might be going on:\n\n**Stress/Burnout**: When you've been running on empty for a long time, your body and brain start to shut down. You might feel exhausted, detached, irritable, or unable to concentrate. Burnout isn't laziness — it's your nervous system saying "enough."\n\n**OCD-like thoughts**: Intrusive thoughts (unwanted, distressing thoughts that pop in) are incredibly common and don't define you. The difference between normal intrusive thoughts and OCD is when you get stuck in a loop trying to neutralize them.\n\n**Social anxiety**: Discomfort around people often stems from a fear of judgment. It can range from mild nervousness to avoiding social situations entirely.\n\n**What helps right now**:\n• **Grounding**: 5-4-3-2-1 technique (name 5 things you see, 4 you hear, 3 you touch, 2 you smell, 1 you taste)\n• **Journaling**: Write down the intrusive thoughts without judging them — just observe.\n• **Small steps**: For social anxiety, start with one low-pressure interaction per day.\n• **Sleep hygiene**: Consistent bedtime, no screens 30min before, cool dark room.\n\nThis is general information, not a diagnosis. A therapist can help you understand your specific patterns and find what works for you."
        }
    };

    function getFallbackResponse(text) {
        const lower = text.toLowerCase();
        // Mental health context - much richer matching
        if (type === 'mental') {
            if (/doctor|appointment|professional|ask|questions|tell.*therapist|therapist|counselor/i.test(lower)) return findFallback('doctor');
            if (/worse|emergency|urgent|hospital|bad|serious|danger|when.*go|call.*911|kill|die|harm|hurt.*self|suicide/i.test(lower)) return findFallback('worse');
            if (/understand|explain|what.*going|what.*wrong|what.*is|tell.*more|what.*mean|why.*feel|what.*happening|help.*me.*understand|what.*think|what.*might/i.test(lower)) return findFallback('understand');
            if (/normal|common|everyone|often|is.*it.*bad|should.*i.*worry|anxious|worried/i.test(lower)) {
                const concerns = (context.symptoms || []).join(' ').toLowerCase();
                if (/ocd|intrusive/i.test(lower) || /ocd|intrusive/i.test(concerns)) return "Intrusive thoughts are incredibly common — studies suggest up to 94% of people experience them. The key difference is whether you get stuck trying to neutralize them. If these thoughts are causing you distress or taking up a lot of your time, that's worth exploring with a professional. In the meantime, try this: when an intrusive thought comes, label it as 'just a thought' and let it pass without engaging. Don't fight it — just observe it like a cloud passing by.\n\nThis is general information, not a diagnosis.";
                if (/social|people|judge/i.test(lower) || /social/i.test(concerns)) return "Social discomfort is extremely common — you're definitely not alone. Many people feel anxious around others, and it often stems from a fear of being judged or not fitting in. The good news is that social anxiety is one of the most treatable conditions. Small steps help: start with brief, low-stakes interactions (ordering coffee, saying hi to a neighbor) and gradually build up.\n\nThis is general information, not a diagnosis.";
                if (/stress|burnout|overwhelm/i.test(lower) || /stress|burnout/i.test(concerns)) return "It's completely normal to feel overwhelmed when you've been dealing with stress for a long time. Your body is telling you it needs a break. Some things that help: setting boundaries (saying no to non-essential tasks), taking short breaks every 90 minutes, and making sure you're sleeping enough. Burnout isn't a personal failure — it's a response to prolonged stress.\n\nThis is general information, not a diagnosis.";
                return "It's normal to feel uncertain about your mental health. The fact that you're looking into it shows you're taking care of yourself. Many people go through periods where things feel off, and often it's a combination of stress, sleep, and life circumstances rather than something clinical. That said, if it's been going on for a while or is affecting your daily life, it's worth discussing with a professional.\n\nThis is general information, not a diagnosis.";
            }
            if (/cope|coping|deal|manage|strategy|technique|help|what.*do|what.*can.*i/i.test(lower)) return "Here are some evidence-based coping strategies that can help:\n\n**For anxiety/stress**:\n• Box breathing: inhale 4 sec, hold 4 sec, exhale 4 sec, hold 4 sec\n• 5-4-3-2-1 grounding: name 5 things you see, 4 hear, 3 touch, 2 smell, 1 taste\n• Progressive muscle relaxation: tense then release each muscle group\n\n**For intrusive thoughts**:\n• Label them: 'That's just a thought, not reality'\n• Don't fight them — observe without engaging\n• Write them down to get them out of your head\n\n**For social anxiety**:\n• Start small: one brief interaction per day\n• Prepare 2-3 conversation topics in advance\n• Remember: most people are too worried about themselves to judge you\n\n**Daily habits that help**:\n• Consistent sleep schedule (same time, even weekends)\n• 20 min of movement daily (walk, stretch, dance)\n• Limit doomscrolling and social media comparison\n• Connect with one person daily, even briefly\n\nThis is general information, not a diagnosis. A therapist can help you find what works best for your specific situation."
            if (/doctor|appointment|professional|ask|questions|tell.*doctor/i.test(lower)) return findFallback('doctor');
            return findFallback('default');
        }
        // Physical context
        if (/doctor|appointment|professional|ask|questions|tell.*doctor/i.test(lower)) return findFallback('doctor');
        if (/worse|emergency|urgent|hospital|bad|serious|danger|when.*go|call.*911/i.test(lower)) return findFallback('worse');
        if (/understand|explain|what.*going|what.*wrong|what.*is|tell.*more|what.*mean/i.test(lower)) return findFallback('understand');
        if (/normal|common|everyone|often|is.*it.*bad|should.*i.*worry|anxious|worried/i.test(lower)) return findFallback('default');
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

    const userAvatarUrl = @json(Auth::check() && Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : '');

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'flex gap-3 justify-end animate-fade-in';

        const bubble = document.createElement('div');
        bubble.className = 'bg-warm-100 rounded-2xl rounded-tr-md p-4 max-w-[85%]';
        const p = document.createElement('p');
        p.className = 'text-sm text-warm-700 leading-relaxed';
        p.textContent = text;
        bubble.appendChild(p);

        const avatar = document.createElement('div');
        if (userAvatarUrl) {
            const img = document.createElement('img');
            img.src = userAvatarUrl;
            img.alt = 'You';
            img.className = 'w-8 h-8 rounded-full object-cover shrink-0';
            avatar.appendChild(img);
        } else {
            avatar.className = 'w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center shrink-0';
            avatar.innerHTML = '<svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
        }

        div.appendChild(bubble);
        div.appendChild(avatar);
        chat.appendChild(div);
        div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function addAIMessage(text) {
        const div = document.createElement('div');
        div.className = 'flex gap-3 animate-fade-in';

        // Build avatar
        const avatar = document.createElement('div');
        avatar.className = 'w-8 h-8 rounded-full bg-gradient-to-br from-' + themeClass + '-300 to-' + themeClass + '-500 flex items-center justify-center shrink-0';
        avatar.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>';

        // Build message bubble
        const bubble = document.createElement('div');
        bubble.className = 'bg-' + themeClass + '-50 border border-' + themeClass + '-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]';

        const content = document.createElement('div');
        content.className = 'text-sm text-' + themeClass + '-700 leading-relaxed space-y-2';

        // Split by paragraphs and render safely
        const paragraphs = text.split(/\n\n+/);
        paragraphs.forEach(function(p) {
            const para = document.createElement('p');
            // Handle bold text
            const parts = p.split(/\*\*(.*?)\*\*/g);
            parts.forEach(function(part, i) {
                if (i % 2 === 1) {
                    const strong = document.createElement('strong');
                    strong.textContent = part;
                    para.appendChild(strong);
                } else {
                    // Handle line breaks within paragraph
                    const lines = part.split(/\n/);
                    lines.forEach(function(line, j) {
                        if (j > 0) para.appendChild(document.createElement('br'));
                        para.appendChild(document.createTextNode(line));
                    });
                }
            });
            content.appendChild(para);
        });

        bubble.appendChild(content);
        div.appendChild(avatar);
        div.appendChild(bubble);
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
                console.log('AI response status:', res.status);
                const rawText = await res.text();
                console.log('AI raw response:', rawText.substring(0, 200));
                let data;
                try { data = JSON.parse(rawText); } catch(e) { data = {}; }

                if (data.response) {
                    history.push({ role: 'assistant', content: data.response });
                    addAIMessage(data.response);
                } else {
                    console.log('AI no response, falling back. Full data:', JSON.stringify(data).substring(0, 300));
                    const fallback = getFallbackResponse(text);
                    history.push({ role: 'assistant', content: fallback });
                    addAIMessage(fallback);
                }
            } catch (err) {
                console.error('AI chat fetch error:', err.message || err);
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
