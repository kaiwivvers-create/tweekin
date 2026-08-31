@extends('layouts.app')

@section('content')
<div class="print-container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    @include('components.download-results', ['title' => 'Physical Screening Results'])

    {{-- Back button --}}
    <a href="{{ route('physical.questions') }}?{{ http_build_query(['symptom' => request('symptom', [])]) }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
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
                <p>This is a <strong>preliminary screening tool</strong>, not a diagnosis. It looks at the patterns in what you've described — symptom type, duration, severity, and combinations — and gives you a general idea of how urgently you might want to follow up. When symptoms are severe or persistent, we'll always recommend seeing a real professional.</p>
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

        // Determine assessment
        if ($urgencyScore >= 4) {
            $urgencyLabel = 'Worth seeing a professional';
            $urgencyColor = 'danger';
            $urgencyAdvice = 'This combination of severity and duration suggests it\'s worth getting checked out. While it could be something minor, having a professional evaluate it would give you peace of mind — and catch anything that might need attention.';
            $urgencyIcon = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z';
        } elseif ($urgencyScore == 3) {
            $urgencyLabel = 'Keep an eye on it';
            $urgencyColor = 'warning';
            $urgencyAdvice = 'This seems like something that could go either way. If it persists for more than a few more days or gets worse, it\'s worth a trip to the doctor. In the meantime, rest and monitor any changes.';
            $urgencyIcon = 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        } else {
            $urgencyLabel = 'Likely manageable';
            $urgencyColor = 'success';
            $urgencyAdvice = 'Based on what you\'ve described, this sounds like something that could resolve on its own. However, if it persists or changes, don\'t hesitate to get it checked out.';
            $urgencyIcon = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        }

        // When to seek help
        $seekHelp = [];
        if ($urgencyScore >= 4) {
            $seekHelp[] = 'Schedule an appointment with your primary care doctor within the next few days';
            $seekHelp[] = 'If symptoms worsen before your appointment, consider urgent care';
        }
        if (in_array('breathing', $presentSymptoms) && $severity >= 3) {
            $seekHelp[] = 'Difficulty breathing can be serious — consider seeing someone sooner rather than later';
        }
        if ($severity >= 4) {
            $seekHelp[] = 'At this severity level, professional evaluation is strongly recommended';
        }
        if ($urgencyScore <= 2) {
            $seekHelp[] = 'Monitor your symptoms for the next few days';
            $seekHelp[] = 'If symptoms persist beyond a week or get worse, see a doctor';
        }

        // Hospital recommendations based on symptoms
        $hospitals = [];
        if (in_array('respiratory', $symptoms) || in_array('breathing', $presentSymptoms)) {
            $hospitals[] = ['name' => 'Pulmonology', 'specialty' => 'Lung and breathing disorders', 'match' => 'Respiratory', 'expect' => 'Breathing tests, chest X-ray', 'tip' => 'Bring a list of when symptoms worsen (exercise, rest, etc.)'];
        }
        if (in_array('pain', $symptoms)) {
            $hospitals[] = ['name' => 'Pain Management Clinic', 'specialty' => 'Body aches, headaches, and general pain', 'match' => 'Pain care', 'expect' => 'Physical exam, possible imaging', 'tip' => 'Rate your pain 1-10 and note what makes it better or worse'];
        }
        if (in_array('skin', $symptoms) || in_array('redness', $presentSymptoms) || in_array('swelling', $presentSymptoms)) {
            $hospitals[] = ['name' => 'Dermatology', 'specialty' => 'Skin conditions, rashes, and irritations', 'match' => 'Skin care', 'expect' => 'Visual exam, possible biopsy', 'tip' => 'Take photos of the area over a few days to show progression'];
        }
        if (in_array('digestive', $symptoms) || in_array('nausea', $presentSymptoms) || in_array('loss-of-appetite', $presentSymptoms)) {
            $hospitals[] = ['name' => 'Gastroenterology', 'specialty' => 'Stomach, digestion, and gut health', 'match' => 'GI care', 'expect' => 'Diet review, blood work, possible scope', 'tip' => 'Keep a food diary for a few days before your visit'];
        }
        if (in_array('fatigue', $symptoms) || in_array('fatigue', $presentSymptoms)) {
            $hospitals[] = ['name' => 'Internal Medicine', 'specialty' => 'Energy, fatigue, and overall health', 'match' => 'General wellness', 'expect' => 'Blood work, vital signs, lifestyle review', 'tip' => 'Note your sleep schedule, stress levels, and diet'];
        }
        if (in_array('fever', $symptoms) || in_array('chills', $presentSymptoms) || in_array('sweating', $presentSymptoms)) {
            $hospitals[] = ['name' => 'Urgent Care', 'specialty' => 'Fevers, infections, and acute symptoms', 'match' => 'Urgent care', 'expect' => 'Vitals, blood test, possible cultures', 'tip' => 'Track your temperature and bring a list of medications taken'];
        }
        $hospitals[] = ['name' => 'Primary Care', 'specialty' => 'General check-ups and first point of contact', 'match' => 'Start here', 'expect' => 'Full physical, blood panel, referral if needed', 'tip' => 'Mention all symptoms, even ones that seem unrelated'];

        // Sources
        $sources = [
            ['name' => 'Mayo Clinic Symptom Checker', 'url' => 'https://www.mayoclinic.org/symptom-checker/select-symptom/itt-20009075'],
            ['name' => 'NHS Health A-Z', 'url' => 'https://www.nhs.uk/conditions/'],
            ['name' => 'CDC Health Information', 'url' => 'https://www.cdc.gov/health-topics'],
        ];
    @endphp

    {{-- Summary card --}}
    <div class="bg-white rounded-2xl border border-warm-200 shadow-sm overflow-hidden mb-6 animate-slide-up">
        {{-- Severity indicator --}}
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

        {{-- What you reported --}}
        <div class="p-6 border-b border-warm-100">
            <h2 class="font-semibold text-warm-800 mb-3">What you reported</h2>
            <div class="space-y-2 text-sm">
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">&#8226;</span>
                    <span class="text-warm-600"><strong>Concerns:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), $symptoms)) }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">&#8226;</span>
                    <span class="text-warm-600"><strong>Duration:</strong> {{ ucfirst(str_replace('-', ' ', $duration)) }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">&#8226;</span>
                    <span class="text-warm-600"><strong>Severity:</strong> {{ $severity }}/5</span>
                </div>
                @if(count($presentSymptoms) > 0 && !in_array('none', $presentSymptoms))
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">&#8226;</span>
                    <span class="text-warm-600"><strong>Additional symptoms:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), $presentSymptoms)) }}</span>
                </div>
                @endif
                @if($additionalInfo)
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">&#8226;</span>
                    <span class="text-warm-600"><strong>Notes:</strong> {{ $additionalInfo }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Assessment --}}
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

                <p class="text-xs text-physical-500 italic pt-2">This is a general assessment based on the information provided. It is not a medical diagnosis. Only a trained professional can properly evaluate your condition.</p>
            </div>
        </div>
    </div>

    {{-- Hospital recommendations carousel --}}
    <div class="mb-6 animate-fade-in" style="animation-delay: 0.2s;">
        <h2 class="font-semibold text-warm-800 mb-3">Recommended care types</h2>
        <p class="text-sm text-warm-500 mb-4">Based on your symptoms, these types of specialists might be the best fit:</p>

        <div id="hospital-carousel" class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($hospitals as $i => $hospital)
            <div class="snap-start shrink-0 w-72 bg-white rounded-2xl border border-warm-200 p-5 hover:border-physical-300 transition-all duration-200 hover:shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-physical-500 bg-physical-50 px-2 py-0.5 rounded-full">{{ $hospital['match'] }}</span>
                </div>
                <h3 class="font-semibold text-warm-800 mb-1">{{ $hospital['name'] }}</h3>
                <p class="text-xs text-warm-500 mb-3">{{ $hospital['specialty'] }}</p>
                <div class="space-y-2 text-xs">
                    <div class="flex items-start gap-1.5">
                        <svg class="w-3.5 h-3.5 text-physical-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-warm-600">{{ $hospital['expect'] }}</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <svg class="w-3.5 h-3.5 text-mental-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-warm-500">{{ $hospital['tip'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Carousel dots --}}
        <div class="flex justify-center gap-1.5 mt-3" id="carousel-dots">
            @foreach($hospitals as $i => $h)
            <button data-index="{{ $i }}" class="carousel-dot w-2 h-2 rounded-full {{ $i === 0 ? 'bg-physical-400' : 'bg-warm-200' }} transition-colors" aria-label="Go to slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>

    {{-- Sources --}}
    <div class="bg-physical-50 border border-physical-200 rounded-2xl p-5 mb-6 animate-fade-in" style="animation-delay: 0.3s;">
        <h3 class="font-semibold text-physical-700 mb-3">Trusted sources</h3>
        <ul class="space-y-2 text-sm text-physical-600">
            @foreach($sources as $source)
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <a href="{{ $source['url'] }}" target="_blank" class="hover:underline">{{ $source['name'] }}</a>
            </li>
            @endforeach
        </ul>
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

    {{-- Action buttons --}}
    <div class="flex flex-col sm:flex-row gap-3 animate-fade-in" style="animation-delay: 0.6s;">
        <a href="{{ route('physical.index') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-physical-200 text-physical-600 font-semibold text-center hover:bg-physical-50 transition-colors">
            Start over
        </a>
        <button onclick="window.print()" class="flex-1 py-3 px-6 rounded-xl bg-physical-400 hover:bg-physical-500 text-white font-semibold transition-colors">
            Save / Print
        </button>
    </div>

</div>

<script>
    // Auto-save screening on page load
    document.addEventListener('DOMContentLoaded', () => {
        const data = {
            type: 'physical',
            title: @json(implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), (array) request('symptom', ['Unknown'])))),
            data: {
                symptom: @json((array) request('symptom', [])),
                duration: @json(request('duration')),
                severity: @json(request('severity')),
                present_symptoms: @json(request('present_symptoms', [])),
                additional_info: @json(request('additional_info', ''))
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
        }).catch(() => {});

        // Carousel dot navigation
        const carousel = document.getElementById('hospital-carousel');
        const dots = document.querySelectorAll('.carousel-dot');
        if (carousel && dots.length) {
            carousel.addEventListener('scroll', () => {
                const scrollLeft = carousel.scrollLeft;
                const cardWidth = 288 + 16; // w-72 + gap-4
                const active = Math.round(scrollLeft / cardWidth);
                dots.forEach((dot, i) => {
                    dot.classList.toggle('bg-physical-400', i === active);
                    dot.classList.toggle('bg-warm-200', i !== active);
                });
            });

            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    const index = parseInt(dot.dataset.index);
                    const cardWidth = 288 + 16;
                    carousel.scrollTo({ left: index * cardWidth, behavior: 'smooth' });
                });
            });
        }
    });
</script>
@endsection
