@extends('layouts.app')

@section('content')
<div class="print-container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    @include('components.download-results', ['title' => 'Physical Screening Results'])

    {{-- Back button --}}
    <a href="{{ route('physical.details') }}?{{ http_build_query(request()->query()) }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </a>

    {{-- Header --}}
    <div class="mb-8 animate-fade-in">
        <div class="w-12 h-12 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-physical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Your assessment</h1>
        <p class="text-warm-500 leading-relaxed">Based on what you told us, here's what we think might be worth considering.</p>
    </div>

    {{-- How this works notice --}}
    <div class="bg-warm-50 border border-warm-200 rounded-2xl p-4 mb-6 animate-fade-in" style="animation-delay: 0.05s;">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg bg-warm-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-sm text-warm-600 leading-relaxed">
                <p class="font-semibold mb-1">How this assessment works</p>
                <p>This is a <strong>preliminary screening tool</strong>, not a diagnosis. It looks at the patterns in what you've described and gives you a general idea of how urgently you might want to follow up.</p>
            </div>
        </div>
    </div>

    @php
        $severity = (int) request('severity', 3);
        $symptoms = (array) request('symptom', []);
        $duration = request('duration', 'unspecified');
        $presentSymptoms = (array) request('present_symptoms', []);
        $additionalInfo = request('additional_info', '');

        // Calculate urgency score
        $urgencyScore = $severity;
        if (in_array($duration, ['week', 'longer'])) $urgencyScore += 1;
        if (in_array('breathing', $presentSymptoms)) $urgencyScore += 1;
        if (in_array('none', $presentSymptoms)) $urgencyScore -= 1;
        $urgencyScore = max(1, min(5, $urgencyScore));

        if ($urgencyScore >= 4) {
            $urgencyLabel = 'Worth seeing a professional';
            $urgencyColor = 'danger';
            $urgencyAdvice = 'This combination of severity and duration suggests it\'s worth getting checked out.';
            $urgencyIcon = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z';
        } elseif ($urgencyScore == 3) {
            $urgencyLabel = 'Keep an eye on it';
            $urgencyColor = 'warning';
            $urgencyAdvice = 'This seems like something that could go either way. If it persists, it\'s worth a trip to the doctor.';
            $urgencyIcon = 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        } else {
            $urgencyLabel = 'Likely manageable';
            $urgencyColor = 'success';
            $urgencyAdvice = 'Based on what you\'ve described, this sounds like something that could resolve on its own.';
            $urgencyIcon = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        }

        $seekHelp = [];
        if ($urgencyScore >= 4) {
            $seekHelp[] = 'Schedule an appointment with your primary care doctor within the next few days';
            $seekHelp[] = 'If symptoms worsen before your appointment, consider urgent care';
        }
        if (in_array('breathing', $presentSymptoms) && $severity >= 3) {
            $seekHelp[] = 'Difficulty breathing can be serious — consider seeing someone sooner rather than later';
        }
        if ($urgencyScore <= 2) {
            $seekHelp[] = 'Monitor your symptoms for the next few days';
            $seekHelp[] = 'If symptoms persist beyond a week or get worse, see a doctor';
        }
    @endphp

    {{-- Summary card --}}
    <div class="bg-white rounded-2xl border border-warm-200 shadow-sm overflow-hidden mb-6 animate-slide-up">
        <div class="p-6 border-b border-warm-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-warm-800">Severity level</h2>
                <span class="text-xs px-3 py-1 rounded-full font-semibold
                    @if($urgencyScore <= 2) bg-success/20 text-success
                    @elseif($urgencyScore <= 3) bg-warning/20 text-warning
                    @else bg-danger/20 text-danger
                    @endif
                ">{{ $urgencyLabel }}</span>
            </div>
            <div class="h-3 bg-warm-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-1000
                    @if($urgencyScore <= 2) bg-success
                    @elseif($urgencyScore <= 3) bg-warning
                    @else bg-danger
                    @endif
                " style="width: {{ $urgencyScore * 20 }}%"></div>
            </div>
            <p class="text-xs text-warm-400 mt-2">Score {{ $urgencyScore }}/5 — based on severity, duration, and symptom combination</p>
        </div>

        <div class="p-6 border-b border-warm-100">
            <h2 class="font-semibold text-warm-800 mb-3">What you reported</h2>
            <div class="space-y-2 text-sm">
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Concerns:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), $symptoms)) }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Duration:</strong> {{ ucfirst(str_replace('-', ' ', $duration)) }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Severity:</strong> {{ $severity }}/5</span>
                </div>
                @if(count($presentSymptoms) > 0 && !in_array('none', $presentSymptoms))
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Additional symptoms:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), $presentSymptoms)) }}</span>
                </div>
                @endif
                @if($additionalInfo)
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Notes:</strong> {{ $additionalInfo }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            <h2 class="font-semibold text-warm-800 mb-3">What this might mean</h2>
            <div class="bg-physical-50 border border-physical-200 rounded-xl p-4 text-sm text-physical-700 leading-relaxed space-y-3">
                <div class="flex gap-3 items-start">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-physical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $urgencyIcon }}"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-physical-600 mb-1">{{ $urgencyLabel }}</p>
                        <p>{{ $urgencyAdvice }}</p>
                    </div>
                </div>
                @if(count($seekHelp) > 0)
                <div class="bg-white rounded-lg p-3 border border-physical-100">
                    <p class="font-semibold text-physical-600 mb-2">What you should do next</p>
                    <ul class="space-y-1.5">
                        @foreach($seekHelp as $item)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-physical-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <p class="text-xs text-physical-500 italic pt-2">This is a general assessment based on the information provided. It is not a medical diagnosis.</p>
            </div>
        </div>
    </div>

    {{-- AI-generated sections container --}}
    <div id="ai-sections" class="space-y-6 mb-6">

        @php $loadingSpinner = '<div class="flex flex-col items-center justify-center py-8 gap-3"><div class="relative w-12 h-12"><div class="absolute inset-0 rounded-full border-[3px] border-physical-200 border-t-physical-500 animate-spin" style="animation-duration: 0.9s;"></div><div class="absolute inset-1 rounded-full border-[3px] border-mental-200 border-b-mental-500 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div></div><p class="text-xs text-warm-400 font-medium">Generating your results...</p></div>'; @endphp

        <div id="section-patterns" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.1s;">
            <div class="p-5 border-b border-warm-100 bg-physical-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
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
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">What to tell a professional</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        <div id="section-correlation" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.25s;">
            <div class="p-5 border-b border-warm-100 bg-physical-50/30">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">How your symptoms relate to each other</h2>
                </div>
                <p class="text-xs text-warm-500 leading-relaxed">This is a <strong>comparison of patterns</strong> — not a diagnosis.</p>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>
        </div>

        {{-- Specialists section --}}
        <div id="section-specialists" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.3s;">
            <div class="p-5 border-b border-warm-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h2 class="font-display font-bold text-lg text-warm-800">Recommended care types</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content">{!! $loadingSpinner !!}</div>
        </div>

        {{-- Nearby care (location-aware) --}}
        <div id="nearby-care" class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: 0.35s;">
            <div class="p-5 border-b border-warm-100 bg-physical-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <h2 class="font-display font-bold text-lg text-warm-800">Nearby care</h2>
                </div>
            </div>
            <div class="p-5 ai-section-content" id="nearby-care-content">
                <div class="flex flex-col items-center justify-center py-6 gap-3"><div class="relative w-10 h-10"><div class="absolute inset-0 rounded-full border-[3px] border-physical-200 border-t-physical-500 animate-spin" style="animation-duration: 0.9s;"></div><div class="absolute inset-1 rounded-full border-[3px] border-mental-200 border-b-mental-500 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div></div><p class="text-xs text-warm-400 font-medium">Detecting your location...</p></div>
            </div>
        </div>

    </div>

    {{-- AI Conclusion Chat --}}
    @include('components.ai-conclusion', [
        'context' => [
            'type' => 'physical',
            'symptoms' => $symptoms,
            'presentSymptoms' => $presentSymptoms,
            'duration' => $duration,
            'severity' => $severity,
            'urgencyLabel' => $urgencyLabel,
            'seekHelp' => $seekHelp,
        ]
    ])

    {{-- Neurodivergence modal data --}}
    @php
        $nd = [
            'concerns' => $symptoms,
            'presentSymptoms' => $presentSymptoms,
            'duration' => $duration,
            'type' => 'physical',
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
            <a href="{{ route('physical.index') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-physical-200 text-physical-600 font-semibold text-center hover:bg-physical-50 transition-colors">
                Start over
            </a>
            <button onclick="window.print()" class="flex-1 py-3 px-6 rounded-xl bg-physical-400 hover:bg-physical-500 text-white font-semibold transition-colors">
                Save / Print
            </button>
        </div>
    </div>

</div>

<script>
    // AI Section Generation
    (function() {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        var themeClass = 'physical';

        var screeningContext = {
            type: 'physical',
            symptoms: @json((array) request('symptom', [])),
            presentSymptoms: @json((array) request('present_symptoms', [])),
            duration: @json(request('duration')),
            severity: {{ request('severity', 'null') }},
            whyThinking: @json(request('additional_info', '')),
            frequency: @json(request('frequency', '')),
            impact: @json((array) request('impact', [])),
            interference: null,
            tried: @json((array) request('tried', [])),
            talkedTo: null,
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
            el.innerHTML = '<div class="flex flex-col items-center justify-center py-6 gap-3"><div class="relative w-10 h-10"><div class="absolute inset-0 rounded-full border-[3px] border-physical-200 border-t-physical-500 animate-spin" style="animation-duration: 0.9s;"></div><div class="absolute inset-1 rounded-full border-[3px] border-mental-200 border-b-mental-500 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div></div><p class="text-xs text-warm-400 font-medium">Finding care near ' + location + '...</p></div>';
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
            type: 'physical',
            title: {{ json_encode(implode(', ', array_map(function($s) { return ucfirst(str_replace('-', ' ', $s)); }, (array) request('symptom', ['Unknown'])))) }},
            data: {
                symptom: @json((array) request('symptom', [])),
                duration: @json(request('duration')),
                severity: @json(request('severity')),
                present_symptoms: @json(request('present_symptoms', [])),
                additional_info: @json(request('additional_info', '')),
                frequency: @json(request('frequency', '')),
                impact: @json(request('impact', [])),
                tried: @json(request('tried', [])),
                changes: @json(request('changes', [])),
                better_worse: @json(request('better_worse', ''))
            },
            severity: {{ request('severity', 'null') }},
            assessment: @json($urgencyLabel)
        };

        fetch(@json(route('screenings.store')), {
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
