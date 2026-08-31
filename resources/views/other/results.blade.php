@extends('layouts.app')

@section('content')
<div class="print-container max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    @include('components.download-results', ['title' => 'Screening Results'])

    {{-- Back button --}}
    <a href="{{ route('other.index') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </a>

    {{-- Header --}}
    <div class="mb-8 animate-fade-in">
        <div class="w-12 h-12 rounded-xl bg-other-100 border border-other-200 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-other-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Here's what we think</h1>
        <p class="text-warm-500 leading-relaxed">Based on what you shared, here's some guidance on where to go from here.</p>
    </div>

    {{-- Summary card --}}
    <div class="bg-white rounded-2xl border border-warm-200 shadow-sm overflow-hidden mb-6 animate-slide-up">

        {{-- What you described --}}
        <div class="p-6 border-b border-warm-100">
            <h2 class="font-semibold text-warm-800 mb-3">What you described</h2>
            <div class="bg-other-50 border border-other-200 rounded-xl p-4 text-sm text-other-700 leading-relaxed">
                <p>"{{ request('description', 'No description provided') }}"</p>
            </div>
        </div>

        {{-- Assessment --}}
        <div class="p-6">
            <h2 class="font-semibold text-warm-800 mb-4">Our take</h2>

            @if(request('lean') === 'physical')
            <div class="bg-physical-50 border border-physical-200 rounded-xl p-4 text-sm text-physical-700 leading-relaxed space-y-3">
                <p>Even though you weren't sure, the way you described things leans more towards a <strong>physical concern</strong>. We'd suggest heading over to the physical symptoms flow for a more detailed assessment, or scheduling a visit with your doctor to get things checked out.</p>
                <a href="{{ route('physical.index') }}" class="inline-flex items-center gap-2 font-semibold hover:underline">
                    Go to physical symptoms flow
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            @elseif(request('lean') === 'mental')
            <div class="bg-mental-50 border border-mental-200 rounded-xl p-4 text-sm text-mental-700 leading-relaxed space-y-3">
                <p>What you're describing sounds like it could be rooted in <strong>mental or emotional patterns</strong>. Sometimes our bodies manifest stress and anxiety physically. We'd suggest checking out our mental health flow for a deeper look at what might be going on.</p>
                <a href="{{ route('mental.index') }}" class="inline-flex items-center gap-2 font-semibold hover:underline">
                    Go to mental health flow
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            @else
            <div class="bg-other-50 border border-other-200 rounded-xl p-4 text-sm text-other-700 leading-relaxed space-y-3">
                <p>It sounds like this could be a mix of things — which is totally normal. Sometimes physical and mental health are more connected than we think. Given the uncertainty, we'd suggest:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Checking out both flows to see if anything resonates</li>
                    <li>Scheduling a visit with your primary care doctor</li>
                    <li>Not dismissing it — even "weird" or "vague" symptoms are worth paying attention to</li>
                </ul>
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('physical.index') }}" class="inline-flex items-center gap-1 font-semibold hover:underline text-physical-600">
                        Physical flow →
                    </a>
                    <a href="{{ route('mental.index') }}" class="inline-flex items-center gap-1 font-semibold hover:underline text-mental-600">
                        Mental flow →
                    </a>
                </div>
            </div>
            @endif

            <p class="text-xs text-warm-400 italic pt-4">This is general guidance, not a medical diagnosis. A professional can help you sort through what's going on more thoroughly.</p>
        </div>
    </div>

    {{-- AI Conclusion Chat --}}
    @include('components.ai-conclusion', [
        'context' => [
            'type' => 'other',
            'symptoms' => [request('lean', 'general')],
            'presentSymptoms' => [],
            'duration' => request('duration'),
            'severity' => null,
            'urgencyLabel' => 'Worth following up',
            'seekHelp' => [
                'Since you weren't sure which path fits, consider trying both screening flows',
                'A primary care visit can help you figure out whether this is physical, mental, or both',
            ],
        ]
    ])

    {{-- Action buttons --}}
    <div class="flex flex-col sm:flex-row gap-3 animate-fade-in" style="animation-delay: 0.6s;">
        <a href="{{ route('home') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-other-200 text-other-600 font-semibold text-center hover:bg-other-50 transition-colors">
            Start over
        </a>
        <button onclick="window.print()" class="flex-1 py-3 px-6 rounded-xl bg-other-400 hover:bg-other-500 text-white font-semibold transition-colors">
            Save / Print
        </button>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const data = {
            type: 'other',
            title: '{{ ucfirst(request("lean", "Not sure")) }}',
            data: {
                description: @json(request('description', '')),
                lean: '{{ request('lean') }}',
                duration: '{{ request('duration') }}'
            },
            severity: null,
            assessment: 'Other screening completed'
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
