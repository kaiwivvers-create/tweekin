@php
    $type = $context['type'] ?? 'general';
    $themeClass = match($type) {
        'physical' => 'physical',
        'mental' => 'mental',
        default => 'other',
    };
    $severity = $context['severity'] ?? null;
    $duration = $context['duration'] ?? null;
    $urgencyLabel = $context['urgencyLabel'] ?? 'moderate concern';
    $symptomList = implode(', ', array_map(fn($s) => strtolower(str_replace('-', ' ', $s)), $context['symptoms'] ?? []));
    $geminiConfigured = (new \App\Services\GeminiChatService())->isConfigured();
    $userAvatar = Auth::check() && Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : '';
    $configData = json_encode([
        'type' => $type,
        'geminiConfigured' => $geminiConfigured,
        'context' => $context,
        'chatUrl' => route('api.chat'),
        'userAvatarUrl' => $userAvatar,
        'themeClass' => $themeClass,
    ]);
@endphp

<div class="bg-white rounded-2xl border border-warm-200 shadow-sm overflow-hidden mb-6 animate-fade-in" style="animation-delay: 0.5s;" id="ai-conclusion-{{ $type }}" data-ai-config="{{ $configData }}">
    {{-- Header --}}
    <div class="p-5 border-b border-warm-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-{{ $themeClass }}-300 to-{{ $themeClass }}-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-warm-800">In conclusion</h3>
                <p class="text-xs text-warm-400">{{ $geminiConfigured ? 'Ask me anything about your results' : 'General guidance based on your screening' }}</p>
            </div>
            @unless($geminiConfigured)
                <div class="ml-auto"><span class="text-[10px] text-warm-400 bg-warm-100 px-2 py-0.5 rounded-full">Limited mode</span></div>
            @endunless
        </div>
    </div>

    {{-- Chat messages --}}
    <div class="ai-chat-messages p-5 space-y-4 min-h-[200px] max-h-[500px] overflow-y-auto">
        {{-- Opening message --}}
        <div class="flex gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-{{ $themeClass }}-300 to-{{ $themeClass }}-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="bg-{{ $themeClass }}-50 border border-{{ $themeClass }}-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <div class="text-sm text-{{ $themeClass }}-700 leading-relaxed space-y-2">
                    <p>Thanks for going through the screening. Here's a quick wrap-up:</p>
                    @if($symptomList) <p><strong>You reported:</strong> {{ $symptomList }}</p> @endif
                    @if($duration) <p><strong>Duration:</strong> {{ str_replace('-', ' ', $duration) }}</p> @endif
                    @if($severity) <p><strong>Severity:</strong> {{ $severity }}/5</p> @endif
                    @if($urgencyLabel) <p><strong>Assessment:</strong> {{ $urgencyLabel }}</p> @endif
                    <p>Is there anything you'd like to understand better? I can explain what might be going on, suggest questions for your doctor, or help you think through next steps.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="p-4 border-t border-warm-100 bg-warm-50/50">
        <div class="flex items-end gap-2">
            <textarea rows="1" placeholder="{{ $geminiConfigured ? 'Ask a follow-up question...' : 'AI chat not configured' }}" class="ai-chat-input flex-1 px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-sm text-warm-800 placeholder-warm-400 focus:border-{{ $themeClass }}-400 focus:ring-0 resize-none max-h-24 transition-colors" {{ $geminiConfigured ? '' : 'disabled' }}></textarea>
            <button class="ai-chat-send w-10 h-10 rounded-xl bg-{{ $themeClass }}-400 hover:bg-{{ $themeClass }}-500 text-white flex items-center justify-center transition-colors shrink-0 disabled:opacity-40" disabled>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </div>
        <p class="text-[10px] text-warm-400 mt-2 text-center">AI-generated guidance, not medical advice.</p>
    </div>
</div>
