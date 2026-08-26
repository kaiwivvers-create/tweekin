@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    {{-- Back button --}}
    <a href="{{ route('physical.questions') }}?symptom={{ request('symptom') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
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

    {{-- Summary card --}}
    <div class="bg-white rounded-2xl border border-warm-200 shadow-sm overflow-hidden mb-6 animate-slide-up">
        {{-- Severity indicator --}}
        <div class="p-6 border-b border-warm-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-warm-800">Severity level</h2>
                <span class="text-xs px-3 py-1 rounded-full font-semibold
                    @if(request('severity') <= 2) bg-success/20 text-success
                    @elseif(request('severity') <= 3) bg-warning/20 text-warning
                    @else bg-danger/20 text-danger
                    @endif
                ">
                    @if(request('severity') <= 2) Low concern
                    @elseif(request('severity') <= 3) Moderate
                    @else Worth looking into
                    @endif
                </span>
            </div>

            {{-- Severity bar --}}
            <div class="h-3 bg-warm-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-1000
                    @if(request('severity') <= 2) bg-success
                    @elseif(request('severity') <= 3) bg-warning
                    @else bg-danger
                    @endif
                " style="width: {{ request('severity') * 20 }}%"></div>
            </div>
        </div>

        {{-- What you reported --}}
        <div class="p-6 border-b border-warm-100">
            <h2 class="font-semibold text-warm-800 mb-3">What you reported</h2>
            <div class="space-y-2 text-sm">
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Symptom:</strong> {{ ucfirst(str_replace('-', ' ', request('symptom', 'unspecified'))) }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Duration:</strong> {{ ucfirst(str_replace('-', ' ', request('duration', 'unspecified'))) }}</span>
                </div>
                @if(request('present_symptoms'))
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Additional symptoms:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), request('present_symptoms', []))) }}</span>
                </div>
                @endif
                @if(request('additional_info'))
                <div class="flex items-start gap-2">
                    <span class="text-warm-400 shrink-0">•</span>
                    <span class="text-warm-600"><strong>Notes:</strong> {{ request('additional_info') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- AI Summary --}}
        <div class="p-6">
            <h2 class="font-semibold text-warm-800 mb-3">What this might mean</h2>
            <div class="bg-physical-50 border border-physical-200 rounded-xl p-4 text-sm text-physical-700 leading-relaxed space-y-3">
                <p>Based on the information you've provided, here's what we think:</p>

                @if(request('severity') >= 4)
                <div class="bg-white rounded-lg p-3 border border-physical-100">
                    <p class="font-semibold text-physical-600 mb-1">Consider seeing a professional</p>
                    <p>The combination of severity and duration you've described suggests this is worth getting checked out. While it could be something minor, having a professional evaluate it would give you peace of mind — and catch anything that might need attention.</p>
                </div>
                @elseif(request('severity') == 3)
                <div class="bg-white rounded-lg p-3 border border-physical-100">
                    <p class="font-semibold text-physical-600 mb-1">Keep an eye on it</p>
                    <p>This seems like something that could go either way. If it persists for more than a few more days or gets worse, it's worth a trip to the doctor. In the meantime, rest and monitor any changes.</p>
                </div>
                @else
                <div class="bg-white rounded-lg p-3 border border-physical-100">
                    <p class="font-semibold text-physical-600 mb-1">Likely manageable</p>
                    <p>Based on what you've described, this sounds like something that could resolve on its own. However, if it persists or changes, don't hesitate to get it checked out.</p>
                </div>
                @endif

                <p class="text-xs text-physical-500 italic">This is a general assessment based on the information provided. It is not a medical diagnosis. Only a trained professional can properly evaluate your condition.</p>
            </div>
        </div>
    </div>

    {{-- Resources --}}
    <div class="bg-physical-50 border border-physical-200 rounded-2xl p-5 mb-6 animate-fade-in" style="animation-delay: 0.3s;">
        <h3 class="font-semibold text-physical-700 mb-3">Helpful resources</h3>
        <ul class="space-y-2 text-sm text-physical-600">
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <a href="https://www.mayoclinic.org" target="_blank" class="hover:underline">Mayo Clinic — Symptom Checker</a>
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <a href="https://www.nhs.uk" target="_blank" class="hover:underline">NHS — Health A-Z</a>
            </li>
        </ul>
    </div>

    {{-- Action buttons --}}
    <div class="flex flex-col sm:flex-row gap-3 animate-fade-in" style="animation-delay: 0.4s;">
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
            title: '{{ ucfirst(str_replace('-', ' ', request('symptom', "Unknown"))) }}',
            data: {
                symptom: '{{ request('symptom') }}',
                duration: '{{ request('duration') }}',
                severity: '{{ request('severity') }}',
                present_symptoms: @json(request('present_symptoms', [])),
                additional_info: '{{ addslashes(request('additional_info', '')) }}'
            },
            severity: {{ request('severity', 'null') }},
            assessment: 'Physical screening completed'
        };

        fetch('{{ route('screenings.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        }).catch(() => {}); // silently fail for guests without session
    });
</script>
@endsection
