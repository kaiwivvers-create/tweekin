@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    {{-- Back button --}}
    <a href="{{ route('mental.index') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </a>

    {{-- Progress bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-mental-600">Mental health check-in</span>
            <span class="text-xs text-warm-400">Step 2 of 2</span>
        </div>
        <div class="h-1.5 bg-mental-100 rounded-full overflow-hidden">
            <div class="h-full bg-mental-400 rounded-full progress-bar" style="width: 100%"></div>
        </div>
    </div>

    {{-- Header --}}
    <div class="mb-8 animate-fade-in">
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">How can we help?</h1>
        <p class="text-warm-500 leading-relaxed">Choose how you'd like to move forward. There's no wrong answer here.</p>
    </div>

    {{-- Mode Cards --}}
    <div class="space-y-4 stagger-children">

        {{-- Help me understand --}}
        <a href="{{ route('mental.understand') }}?{{ http_build_query(request()->query()) }}&mode=understand" class="group block">
            <div class="relative bg-white rounded-2xl border-2 border-mental-200 p-6 sm:p-8 card-hover overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-mental-100 rounded-bl-[60px] -z-0 opacity-50 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-lg text-warm-800 mb-1">Help me understand</h2>
                        <p class="text-warm-500 text-sm leading-relaxed">
                            Give me some perspective on what might be happening, possible reasons, and whether this is something I should take further. Like a knowledgeable friend who won't just tell me what I want to hear.
                        </p>
                    </div>
                </div>
            </div>
        </a>

        {{-- Just listen --}}
        <a href="{{ route('mental.vent') }}?{{ http_build_query(request()->query()) }}&mode=vent" class="group block">
            <div class="relative bg-white rounded-2xl border-2 border-mental-200 p-6 sm:p-8 card-hover overflow-hidden">
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-mental-100 rounded-tr-[60px] -z-0 opacity-50 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-lg text-warm-800 mb-1">Just listen</h2>
                        <p class="text-warm-500 text-sm leading-relaxed">
                            I just need to get this off my chest. Don't reassure me or diagnose me — just hear me out and help me process what I'm feeling. No pressure, no judgment.
                        </p>
                    </div>
                </div>
            </div>
        </a>

    </div>

    {{-- Safety note --}}
    <div class="mt-8 bg-mental-50 border border-mental-200 rounded-2xl p-4 animate-fade-in" style="animation-delay: 0.3s;">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg bg-mental-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div class="text-sm text-mental-700 leading-relaxed">
                <p class="font-semibold mb-1">A note about AI and mental health</p>
                <p>We want to be upfront: AI can't replace a real human who cares about you. If things feel heavy, please consider reaching out to someone you trust or a professional. We'll always encourage you to do so when it matters most.</p>
            </div>
        </div>
    </div>

</div>
@endsection
