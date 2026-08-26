@extends('layouts.app')

@section('content')
<div class="w-full flex">
    {{-- Sidebar with vertical progress --}}
    <div class="hidden lg:flex w-24 shrink-0 border-r border-warm-200 bg-white/50 py-12 justify-center sticky top-14 h-[calc(100vh-3.5rem)]">
        @include('components.vertical-progress', ['steps' => ['Category', 'Questions', 'Results'], 'current' => 2, 'theme' => 'physical'])
    </div>

    <div class="flex-1 px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
        <a href="{{ route('physical.index') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        <div class="lg:hidden mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-physical-600">Physical symptoms</span>
                <span class="text-xs text-warm-400">Step 2 of 3</span>
            </div>
            <div class="h-1.5 bg-physical-100 rounded-full overflow-hidden">
                <div class="h-full bg-physical-400 rounded-full progress-bar" style="width: 66%"></div>
            </div>
        </div>

        <div class="mb-8 animate-fade-in">
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Tell us more</h1>
            <p class="text-warm-500 leading-relaxed">
                @if(request('symptom') === 'unsure')
                    Answer what you can — we'll use this to help figure out what's going on.
                @else
                    Based on your selection, here are some questions to help narrow things down.
                @endif
            </p>
        </div>

        <form id="physical-questions-form" action="{{ route('physical.results') }}" method="GET" class="space-y-8">
            <input type="hidden" name="symptom" value="{{ request('symptom') }}">

            <div class="animate-fade-in" style="animation-delay: 0.1s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How long have you been dealing with this?</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="today" class="peer hidden" required><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-physical-400 peer-checked:bg-physical-50 peer-checked:text-physical-700 hover:border-warm-300 transition-all duration-200">Just started today</div></label>
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="few-days" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-physical-400 peer-checked:bg-physical-50 peer-checked:text-physical-700 hover:border-warm-300 transition-all duration-200">A few days</div></label>
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="week" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-physical-400 peer-checked:bg-physical-50 peer-checked:text-physical-700 hover:border-warm-300 transition-all duration-200">About a week</div></label>
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="longer" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-physical-400 peer-checked:bg-physical-50 peer-checked:text-physical-700 hover:border-warm-300 transition-all duration-200">Longer than a week</div></label>
                </div>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.2s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How would you rate the severity?</label>
                <div class="flex gap-2">
                    @php
                    $severities = [
                        ['value' => '1', 'label' => 'Mild'],
                        ['value' => '2', 'label' => 'Moderate'],
                        ['value' => '3', 'label' => 'Uncomfortable'],
                        ['value' => '4', 'label' => 'Severe'],
                        ['value' => '5', 'label' => 'Intense'],
                    ];
                    @endphp
                    @foreach($severities as $s)
                    <label class="flex-1 block cursor-pointer">
                        <input type="radio" name="severity" value="{{ $s['value'] }}" class="peer hidden" required>
                        <div class="p-2 sm:p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-6 h-6 rounded-full mx-auto mb-1
                                @if($s['value'] == '1') bg-success/30
                                @elseif($s['value'] == '2') bg-success/50
                                @elseif($s['value'] == '3') bg-warning/50
                                @elseif($s['value'] == '4') bg-warning/70
                                @else bg-danger/50
                                @endif
                            "></div>
                            <div class="text-xs font-medium text-warm-600 peer-checked:text-physical-700">{{ $s['label'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.3s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">Check any that apply:</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $symptomsList = [
                        ['value' => 'fever', 'label' => 'Fever', 'tip' => 'When your body temperature is higher than normal (usually above 100.4F / 38C)'],
                        ['value' => 'chills', 'label' => 'Chills', 'tip' => 'Feeling cold and shivering, even when the room is warm'],
                        ['value' => 'nausea', 'label' => 'Nausea', 'tip' => 'That uneasy, queasy feeling in your stomach — like you might throw up'],
                        ['value' => 'dizziness', 'label' => 'Dizziness', 'tip' => 'Feeling lightheaded, unsteady, or like the room is spinning'],
                        ['value' => 'fatigue', 'label' => 'Fatigue', 'tip' => 'Being really tired even after sleeping — not just normal tiredness'],
                        ['value' => 'sweating', 'label' => 'Sweating', 'tip' => 'Sweating more than usual, or sweating when you normally wouldn\'t'],
                        ['value' => 'loss-of-appetite', 'label' => 'Loss of appetite', 'tip' => 'Not feeling hungry at all, or feeling turned off by food'],
                        ['value' => 'swelling', 'label' => 'Swelling', 'tip' => 'An area of your body looking puffy or bigger than usual'],
                        ['value' => 'redness', 'label' => 'Redness', 'tip' => 'Skin that looks pink, red, or inflamed in a specific area'],
                        ['value' => 'discharge', 'label' => 'Discharge', 'tip' => 'Any fluid or substance coming from a wound, eye, ear, or other area that isn\'t blood'],
                        ['value' => 'breathing', 'label' => 'Difficulty breathing', 'tip' => 'Feeling like you can\'t get enough air, or breathing feels harder than usual'],
                        ['value' => 'none', 'label' => 'None of the above', 'tip' => 'I\'m not experiencing any of these specific symptoms'],
                    ];
                    @endphp
                    @foreach($symptomsList as $s)
                    <label class="group block cursor-pointer relative">
                        <input type="checkbox" name="present_symptoms[]" value="{{ $s['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white text-sm font-medium text-warm-600 peer-checked:border-physical-400 peer-checked:bg-physical-50 peer-checked:text-physical-700 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors group-hover:border-physical-400">
                                <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="flex-1">{{ $s['label'] }}</span>
                            <button type="button" class="tip-trigger w-4 h-4 rounded-full bg-warm-200 hover:bg-warm-300 flex items-center justify-center shrink-0 transition-colors" data-tip="{{ $s['tip'] }}">
                                <svg class="w-2.5 h-2.5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        </div>
                    </label>
                    @endforeach
                </div>
                <div id="tip-tooltip" class="hidden mt-2 bg-physical-50 border border-physical-200 rounded-xl p-3 text-sm text-physical-700 animate-fade-in">
                    <div class="flex gap-2">
                        <svg class="w-4 h-4 text-physical-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="tip-text"></span>
                    </div>
                </div>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.4s;">
                <label for="additional-info" class="block text-sm font-semibold text-warm-700 mb-2">Anything else you want to add? (optional)</label>
                <textarea id="additional-info" name="additional_info" rows="3" placeholder="e.g., 'I also started a new medication last week' or 'This happened after I ate something'" class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 placeholder-warm-400 focus:border-physical-400 focus:ring-0 resize-none transition-colors"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-6 rounded-xl bg-physical-400 hover:bg-physical-500 text-white font-semibold transition-colors duration-200 disabled:opacity-40 disabled:cursor-not-allowed" disabled id="physical-q-submit">Get my assessment</button>
            </div>
        </form>
    </div>
</div>

<script>
    const form = document.getElementById('physical-questions-form');
    const submitBtn = document.getElementById('physical-q-submit');
    const tipTooltip = document.getElementById('tip-tooltip');
    const tipText = document.getElementById('tip-text');

    function checkRequired() {
        const durationSelected = form.querySelector('input[name="duration"]:checked');
        const severitySelected = form.querySelector('input[name="severity"]:checked');
        submitBtn.disabled = !(durationSelected && severitySelected);
    }

    form.querySelectorAll('input[type="radio"]').forEach(r => r.addEventListener('change', checkRequired));

    // Tooltip handling
    document.querySelectorAll('.tip-trigger').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const tip = btn.getAttribute('data-tip');
            tipText.textContent = tip;
            tipTooltip.classList.remove('hidden');
            // Scroll tooltip into view
            tipTooltip.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
</script>
@endsection
