@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    {{-- Back button --}}
    <a href="{{ route('mental.mode') }}?{{ http_build_query(request()->query()) }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </a>

    {{-- Header --}}
    <div class="mb-8 animate-fade-in">
        <div class="w-12 h-12 rounded-xl bg-mental-100 border border-mental-200 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">I'm all ears</h1>
        <p class="text-warm-500 leading-relaxed">No advice, no diagnosis, no trying to fix anything. Just a space to let it out.</p>
    </div>

    {{-- Info card --}}
    <div class="bg-mental-50 border border-mental-200 rounded-2xl p-4 mb-6 animate-fade-in" style="animation-delay: 0.1s;">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg bg-mental-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-sm text-mental-700 leading-relaxed">
                <p>Write whatever you need to. This is your space. We won't try to solve your problems or tell you everything will be okay — we'll just acknowledge what you're going through.</p>
            </div>
        </div>
    </div>

    {{-- Chat container --}}
    <div id="vent-container" class="space-y-4 mb-6 min-h-[200px]">

        {{-- User's context --}}
        <div class="flex gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="bg-warm-100 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <p class="text-sm text-warm-700 leading-relaxed">
                    I've been thinking about <span class="font-semibold text-mental-600">{{ ucfirst(str_replace('-', ' ', request('concern', 'how I've been feeling'))) }}</span>.
                    @if(request('why_thinking'))
                        <br><span class="text-warm-500">"{{ request('why_thinking') }}"</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- AI acknowledges --}}
        <div class="flex gap-3 animate-fade-in" style="animation-delay: 0.15s;">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-xs">T</span>
            </div>
            <div class="bg-mental-50 border border-mental-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <p class="text-sm text-mental-700 leading-relaxed">I hear you. That sounds like a lot to be carrying around. Take your time — I'm here whenever you're ready to talk more about it.</p>
            </div>
        </div>

    </div>

    {{-- Input area --}}
    <div class="bg-white border-2 border-warm-200 rounded-2xl p-3 flex items-end gap-2 animate-fade-in" style="animation-delay: 0.3s;">
        <textarea
            id="vent-input"
            rows="1"
            placeholder="Say whatever you need to..."
            class="flex-1 px-3 py-2 text-sm text-warm-800 placeholder-warm-400 focus:outline-none resize-none max-h-32 bg-transparent"
        ></textarea>
        <button
            id="vent-send"
            class="w-9 h-9 rounded-xl bg-mental-400 hover:bg-mental-500 text-white flex items-center justify-center transition-colors shrink-0 disabled:opacity-40"
            disabled
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>

    {{-- Safety check (hidden by default) --}}
    <div id="safety-message" class="hidden mt-4 bg-danger/10 border border-danger/20 rounded-2xl p-4 animate-fade-in">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg bg-danger/20 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div class="text-sm text-danger leading-relaxed">
                <p class="font-semibold mb-1">We're concerned about what you've shared.</p>
                <p>What you're describing sounds serious, and we want to make sure you get the right support. Please consider reaching out to someone who can help:</p>
                <ul class="mt-2 space-y-1">
                    <li>• <strong>Crisis Text Line:</strong> Text HOME to 741741</li>
                    <li>• <strong>988 Suicide & Crisis Lifeline:</strong> Call or text 988</li>
                    <li>• <strong>Emergency:</strong> Call 911</li>
                </ul>
                <p class="mt-2 font-semibold">You deserve real support from people trained to help.</p>
            </div>
        </div>
    </div>

    <script>
        const input = document.getElementById('vent-input');
        const sendBtn = document.getElementById('vent-send');
        const container = document.getElementById('vent-container');
        const safetyMessage = document.getElementById('safety-message');

        // Auto-resize textarea
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 128) + 'px';
            sendBtn.disabled = input.value.trim().length === 0;
        });

        // Simple AI response simulation
        const responses = [
            "I hear you. That sounds really difficult to deal with.",
            "Thanks for sharing that with me. It takes courage to put these feelings into words.",
            "That makes sense. It's completely valid to feel that way.",
            "I'm listening. There's no rush — take as much time as you need.",
            "What you're feeling matters. You don't need to justify it to anyone.",
            "That sounds exhausting. It's okay to not be okay sometimes.",
            "I appreciate you opening up. Sometimes just saying it out loud helps.",
        ];

        const safetyKeywords = ['suicide', 'kill myself', 'end it', 'self harm', 'hurt myself', 'want to die', 'no reason to live'];

        function addMessage(text, isUser = true) {
            const div = document.createElement('div');
            div.className = 'flex gap-3 animate-fade-in';
            div.innerHTML = isUser ? `
                <div class="w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="bg-warm-100 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                    <p class="text-sm text-warm-700 leading-relaxed">${text}</p>
                </div>
            ` : `
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-xs">T</span>
                </div>
                <div class="bg-mental-50 border border-mental-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                    <p class="text-sm text-mental-700 leading-relaxed">${text}</p>
                </div>
            `;
            container.appendChild(div);
            div.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }

        sendBtn.addEventListener('click', () => {
            const text = input.value.trim();
            if (!text) return;

            addMessage(text, true);
            input.value = '';
            input.style.height = 'auto';
            sendBtn.disabled = true;

            // Check for safety concerns
            const lowerText = text.toLowerCase();
            const isConcerning = safetyKeywords.some(kw => lowerText.includes(kw));

            if (isConcerning) {
                safetyMessage.classList.remove('hidden');
            }

            // Simulate AI response
            setTimeout(() => {
                const response = isConcerning
                    ? "I want you to know that what you're feeling is valid, and you deserve support from someone who can truly help. Please consider reaching out to a crisis line — they're available 24/7 and you don't have to go through this alone."
                    : responses[Math.floor(Math.random() * responses.length)];
                addMessage(response, false);
            }, 1000);
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });
    </script>

</div>
@endsection
