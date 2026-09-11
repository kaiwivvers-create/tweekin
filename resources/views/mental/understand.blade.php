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
    </div>

    {{-- AI-generated sections container --}}
    <div id="ai-sections" class="space-y-6 mb-6">

        @php $loadingSpinner = '<div class="flex flex-col items-center justify-center py-8 gap-3"><div class="relative w-12 h-12"><div class="absolute inset-0 rounded-full border-[3px] border-mental-200 border-t-mental-500 animate-spin" style="animation-duration: 0.9s;"></div><div class="absolute inset-1 rounded-full border-[3px] border-physical-200 border-b-physical-500 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div></div><p class="text-xs text-warm-400 font-medium">Generating your results...</p></div>'; @endphp

        <div id="section-patterns" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.1s;">
            <div class="p-5 border-b border-warm-100 bg-mental-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">What this pattern often involves</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        <div id="section-strategies" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.15s;">
            <div class="p-5 border-b border-warm-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-success/15 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">Practical strategies</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        <div id="section-what-to-tell" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.2s;">
            <div class="p-5 border-b border-warm-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">What to tell a professional</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        <div id="section-correlation" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.25s;">
            <div class="p-5 border-b border-warm-100 bg-mental-50/30">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">How your symptoms relate to each other</h2>
                </div>
                <p class="text-xs text-warm-500 leading-relaxed">This is a <strong>comparison of patterns</strong> — not a diagnosis.</p>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        <div id="section-specialists" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.3s;">
            <div class="p-5 border-b border-warm-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">Recommended support</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        {{-- Nearby care (location-aware) --}}
        <div id="nearby-care" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.35s;">
            <div class="p-5 border-b border-warm-100 bg-mental-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">Nearby care</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content" id="nearby-care-content">
                <div class="flex flex-col items-center justify-center py-6 gap-3"><div class="relative w-10 h-10"><div class="absolute inset-0 rounded-full border-[3px] border-mental-200 border-t-mental-500 animate-spin" style="animation-duration: 0.9s;"></div><div class="absolute inset-1 rounded-full border-[3px] border-physical-200 border-b-physical-500 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div></div><p class="text-xs text-warm-400 font-medium">Detecting your location...</p></div>
            </div>
        </div>

    </div>

    {{-- AI Conclusion Chat --}}
    @include('components.ai-conclusion', [
        'context' => [
            'type' => 'mental',
            'symptoms' => (array) request('concern', ['general']),
            'presentSymptoms' => (array) request('physical_signs', []),
            'duration' => request('duration'),
            'frequency' => request('frequency'),
            'impact' => (array) request('impact', []),
            'interference' => request('interference'),
            'talkedTo' => request('talked_to'),
            'tried' => (array) request('tried', []),
            'severity' => null,
            'urgencyLabel' => 'Worth exploring further',
            'seekHelp' => [
                'Consider speaking with a licensed therapist or counselor for a professional perspective',
                'If symptoms persist or worsen, your primary care doctor can also help and refer you to specialists',
            ],
        ]
    ])

    {{-- Neurodivergence modal data --}}
    @php
        $nd = [
            'concerns' => (array) request('concern', []),
            'presentSymptoms' => (array) request('physical_signs', []),
            'duration' => request('duration'),
            'type' => 'mental',
            'whyThinking' => request('why_thinking', ''),
            'frequency' => request('frequency', ''),
            'impact' => (array) request('impact', []),
            'interference' => request('interference', ''),
            'talkedTo' => request('talked_to', ''),
            'tried' => (array) request('tried', []),
        ];
    @endphp
    <script>window._neurodivergenceModalData = @json($nd);</script>

    {{-- Neurodivergence + action buttons, centered --}}
    <div class="flex flex-col items-center gap-3 mt-6 animate-fade-in" style="animation-delay: 0.5s;">
        <button onclick="openModalWithData('neurodivergent', window._neurodivergenceModalData)" class="flex items-center gap-2 px-4 py-2 rounded-full bg-mental-400/90 hover:bg-mental-500 text-white text-sm font-medium shadow-sm transition-transform hover:scale-[1.02]" title="Could this be related to neurodivergence?">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <span>Could this be related to neurodivergence?</span>
        </button>

        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <a href="{{ route('mental.index') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-mental-200 text-mental-600 font-semibold text-center hover:bg-mental-50 transition-colors">
                Start over
            </a>
            <button onclick="openModal('resources')" class="flex-1 py-3 px-6 rounded-xl bg-mental-400 hover:bg-mental-500 text-white font-semibold transition-colors">
                Show me resources
            </button>
        </div>
    </div>

</div>

<script>
    (function() {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        var themeClass = 'mental';

        var screeningContext = {
            type: 'mental',
            symptoms: @json((array) request('concern', [])),
            presentSymptoms: @json((array) request('physical_signs', [])),
            duration: @json(request('duration')),
            severity: null,
            whyThinking: @json(request('why_thinking', '')),
            frequency: @json(request('frequency', '')),
            impact: @json((array) request('impact', [])),
            interference: @json(request('interference', '')),
            tried: @json((array) request('tried', [])),
            talkedTo: @json(request('talked_to', '')),
            changes: @json((array) request('changes', [])),
            betterWorse: @json(request('better_worse', '')),
            location: null
        };

        var sections = ['patterns', 'strategies', 'what-to-tell', 'correlation', 'specialists'];

        document.addEventListener('DOMContentLoaded', function() {
            getUserLocation().then(function(loc) {
                if (loc) screeningContext.location = loc;
                sections.forEach(function(s) { fetchAISection(s, screeningContext, csrfToken, themeClass); });
                fetchNearbyCare(loc, screeningContext, csrfToken);
            });
        });

        function fetchNearbyCare(location, ctx, csrf) {
            var el = document.getElementById('nearby-care-content');
            if (!el) return;
            if (!location) {
                el.innerHTML = '<p class="text-sm text-warm-500 italic">Enable location permissions to see care options near you.</p>';
                return;
            }
            el.innerHTML = '<div class="flex flex-col items-center justify-center py-6 gap-3"><div class="relative w-10 h-10"><div class="absolute inset-0 rounded-full border-[3px] border-mental-200 border-t-mental-500 animate-spin" style="animation-duration: 0.9s;"></div><div class="absolute inset-1 rounded-full border-[3px] border-physical-200 border-b-physical-500 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div></div><p class="text-xs text-warm-400 font-medium">Finding care near ' + location + '...</p></div>';
            fetch('/api/generate-section', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ section: 'specialists', context: Object.assign({}, ctx, { location: location }) })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.content) {
                    renderAIMarkdown(el, '### Care options near ' + location + '\n\n' + data.content, themeClass);
                } else {
                    el.innerHTML = '<p class="text-sm text-warm-500 italic">Could not load nearby options.</p>';
                }
            })
            .catch(function() {
                el.innerHTML = '<p class="text-sm text-warm-500 italic">Could not load nearby options.</p>';
            });
        }
    })();

    // Auto-save screening on page load
    document.addEventListener('DOMContentLoaded', () => {
        const data = {
            type: 'mental',
            title: '{{ collect((array) request("concern", ["Mental health"]))->map(fn($c) => ucfirst(str_replace("-", " ", $c)))->implode(", ") }}',
            data: {
                concern: @json((array) request('concern', [])),
                why_thinking: @json(request('why_thinking', '')),
                duration: '{{ request('duration') }}',
                triggers: @json(request('triggers', '')),
                frequency: @json(request('frequency', '')),
                impact: @json(request('impact', [])),
                physical_signs: @json(request('physical_signs', [])),
                talked_to: @json(request('talked_to', '')),
                tried: @json(request('tried', [])),
                interference: @json(request('interference', '')),
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
        }).then(function(res) {
            if (!res.ok) console.error('Screening save failed:', res.status);
        }).catch(function(err) {
            console.error('Screening save error:', err);
        });
    });
</script>
@endsection
