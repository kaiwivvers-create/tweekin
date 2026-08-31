@extends('layouts.app')

@section('content')
<div class="print-container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    @include('components.download-results', ['title' => 'Mental Health Screening Results'])

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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Let's figure this out</h1>
        <p class="text-warm-500 leading-relaxed">We'll give you some perspective on what you've shared — not a diagnosis, just some things to think about.</p>
    </div>

    {{-- How this works notice --}}
    <div class="bg-warm-50 border border-warm-200 rounded-2xl p-4 mb-6 animate-fade-in" style="animation-delay: 0.05s;">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg bg-warm-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-sm text-warm-600 leading-relaxed">
                <p class="font-semibold mb-1">How this works</p>
                <p>This is a <strong>preliminary screening tool</strong>, not a diagnosis. We look at the patterns in what you've described and give you general perspective. If things seem more serious, we'll always recommend reaching out to a real professional. When severity is high, we provide crisis resources immediately.</p>
            </div>
        </div>
    </div>

    {{-- Chat-like interface --}}
    <div id="chat-container" class="space-y-4 mb-6">

        {{-- User's input summary --}}
        <div class="flex gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="bg-warm-100 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <p class="text-sm text-warm-700 leading-relaxed">
                    <span class="font-semibold">You shared:</span> You think you might be dealing with
                    @php $concerns = (array) request('concern', []); @endphp
                    @if(count($concerns) > 0)
                        <span class="font-semibold text-mental-600">{{ collect($concerns)->map(fn($c) => ucfirst(str_replace('-', ' ', $c)))->implode(', ') }}</span>
                    @else
                        <span class="font-semibold text-mental-600">something on your mind</span>
                    @endif
                    @if(request('why_thinking'))
                        <br><span class="text-warm-500">"{{ request('why_thinking') }}"</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- AI Response placeholder --}}
        <div class="flex gap-3 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-xs">T</span>
            </div>
            <div class="bg-mental-50 border border-mental-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]">
                <div class="text-sm text-mental-700 leading-relaxed space-y-3">
                    <p>Thanks for sharing that. Here are a few things worth considering:</p>

                    <div class="bg-white rounded-xl p-3 border border-mental-100">
                        <p class="font-semibold text-mental-600 mb-1">What you described</p>
                        <p>The patterns you're noticing are valid reasons to look into this further. Whether it turns out to be what you think or something else entirely, paying attention to how you feel is always a good first step.</p>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-mental-100">
                        <p class="font-semibold text-mental-600 mb-1">Some perspective</p>
                        <p>Online content can be helpful for understanding yourself, but it's easy to see yourself in every description you read. A professional can help you sort through what's real and what might be coincidence.</p>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-mental-100">
                        <p class="font-semibold text-mental-600 mb-1">What we'd suggest</p>
                        <p>This seems like something worth taking to a professional — not because it's necessarily serious, but because having an expert's perspective can give you clarity and peace of mind.</p>
                    </div>

                    <p class="text-xs text-warm-400 italic pt-2">Remember: this is general information, not a diagnosis. Only a trained professional can properly evaluate what you're experiencing.</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Specialist recommendations --}}
    @php
        $concerns = (array) request('concern', []);
        $specialists = [];
        if (in_array('anxiety', $concerns)) {
            $specialists[] = ['name' => 'Anxiety & Stress Specialist', 'specialty' => 'Worry, panic, and physical tension', 'expect' => 'Talking through triggers, learning coping tools', 'tip' => 'Write down your top 3 worries before your session'];
        }
        if (in_array('depression', $concerns)) {
            $specialists[] = ['name' => 'Depression & Mood Counselor', 'specialty' => 'Persistent sadness, low energy, withdrawal', 'expect' => 'Mood assessment, goal-setting, therapy options', 'tip' => 'There is no wrong way to feel — be honest about what you share'];
        }
        if (in_array('stress', $concerns)) {
            $specialists[] = ['name' => 'Burnout & Stress Coach', 'specialty' => 'Overwhelm, exhaustion, work-life balance', 'expect' => 'Lifestyle review, boundary-setting strategies', 'tip' => 'Note which parts of your day feel heaviest'];
        }
        if (in_array('ocd', $concerns)) {
            $specialists[] = ['name' => 'OCD & Intrusive Thoughts', 'specialty' => 'Repetitive thoughts and compulsive behaviors', 'expect' => 'Thought pattern review, ERP therapy overview', 'tip' => 'Try not to fight the thoughts — observe them without acting'];
        }
        if (in_array('social', $concerns)) {
            $specialists[] = ['name' => 'Social Anxiety Therapist', 'specialty' => 'Discomfort in social situations, fear of judgment', 'expect' => 'Gradual exposure planning, confidence-building', 'tip' => 'Start small — one low-pressure social interaction at a time'];
        }
        if (in_array('sleep', $concerns)) {
            $specialists[] = ['name' => 'Sleep & Insomnia Clinic', 'specialty' => 'Difficulty falling or staying asleep', 'expect' => 'Sleep diary review, hygiene tips, CBT-I options', 'tip' => 'Track your sleep for a week before your appointment'];
        }
        if (in_array('trauma', $concerns)) {
            $specialists[] = ['name' => 'Trauma-Informed Therapist', 'specialty' => 'Past experiences affecting your present', 'expect' => 'Safe space to process, EMDR or CPT options', 'tip' => 'Go at your own pace — you are in control of what you share'];
        }
        if (empty($specialists)) {
            $specialists[] = ['name' => 'Licensed Therapist', 'specialty' => 'General mental health and self-understanding', 'expect' => 'Open conversation, coping strategies, referrals', 'tip' => 'Come as you are — no preparation needed'];
        }
        $specialists[] = ['name' => 'Your Primary Care Doctor', 'specialty' => 'General health check, medication options, referrals', 'expect' => 'Brief screening, blood work if needed', 'tip' => 'Mention your mental health concerns alongside physical ones'];
    @endphp

    <div class="bg-white rounded-2xl border border-warm-200 p-5 mb-6 animate-fade-in" style="animation-delay: 0.3s;">
        <h2 class="font-semibold text-warm-800 mb-2">Recommended support</h2>
        <p class="text-sm text-warm-500 mb-4">These specialists can help with what you described:</p>
        <div class="flex gap-4 overflow-x-auto pb-3 snap-x snap-mandatory scroll-smooth" style="scrollbar-width: none;">
            @foreach($specialists as $s)
            <div class="snap-start shrink-0 w-72 bg-mental-50/50 rounded-2xl border border-mental-200 p-4">
                <h3 class="font-semibold text-warm-800 mb-1">{{ $s['name'] }}</h3>
                <p class="text-xs text-warm-500 mb-3">{{ $s['specialty'] }}</p>
                <div class="space-y-2 text-xs">
                    <div class="flex items-start gap-1.5">
                        <svg class="w-3.5 h-3.5 text-mental-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-warm-600">{{ $s['expect'] }}</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <svg class="w-3.5 h-3.5 text-physical-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-warm-500">{{ $s['tip'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- AI Conclusion Chat --}}
    @include('components.ai-conclusion', [
        'context' => [
            'type' => 'mental',
            'symptoms' => (array) request('concern', ['general']),
            'presentSymptoms' => [],
            'duration' => request('duration'),
            'severity' => null,
            'urgencyLabel' => 'Worth exploring further',
            'seekHelp' => [
                'Consider speaking with a licensed therapist or counselor for a professional perspective',
                'If symptoms persist or worsen, your primary care doctor can also help and refer you to specialists',
            ],
        ]
    ])

    {{-- Action buttons --}}
    <div class="flex flex-col sm:flex-row gap-3 animate-fade-in" style="animation-delay: 0.6s;">
        <a href="{{ route('mental.index') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-mental-200 text-mental-600 font-semibold text-center hover:bg-mental-50 transition-colors">
            Start over
        </a>
        <button onclick="openModal('resources')" class="flex-1 py-3 px-6 rounded-xl bg-mental-400 hover:bg-mental-500 text-white font-semibold transition-colors">
            Show me resources
        </button>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const data = {
            type: 'mental',
            title: '{{ collect((array) request("concern", ["Mental health"]))->map(fn($c) => ucfirst(str_replace("-", " ", $c)))->implode(", ") }}',
            data: {                    concern: @json((array) request('concern', [])),
                why_thinking: @json(request('why_thinking', '')),
                duration: '{{ request('duration') }}',
                triggers: @json(request('triggers', '')),
                mode: 'understand'
            },
            severity: null,
            assessment: 'Mental health screening completed'
        };

        fetch('{{ route('screenings.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).catch(() => {});
    });
</script>
@endsection
