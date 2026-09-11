@extends('layouts.app')

@section('content')
<div class="w-full flex">
    {{-- Sidebar with vertical progress --}}
    <div class="hidden lg:flex w-24 shrink-0 border-r border-mental-100 bg-mental-50/30 py-12 justify-center sticky top-14 h-[calc(100vh-3.5rem)]">
        @include('components.vertical-progress', ['steps' => ['Concerns', 'Details', 'Mode', 'Results'], 'current' => 2, 'theme' => 'mental'])
    </div>

    <div class="flex-1 px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
        <a href="{{ route('mental.index') }}?{{ http_build_query(request()->except('frequency', 'impact', 'physical_signs', 'talked_to', 'tried', 'interference')) }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        <div class="lg:hidden mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-mental-600">Mental health check-in</span>
                <span class="text-xs text-warm-400">Step 2 of 4</span>
            </div>
            <div class="h-1.5 bg-mental-100 rounded-full overflow-hidden">
                <div class="h-full bg-mental-400 rounded-full progress-bar" style="width: 50%"></div>
            </div>
        </div>

        <div class="mb-8 animate-fade-in">
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">A bit more about how it's showing up</h1>
            <p class="text-warm-500 leading-relaxed">These details help us understand the full picture — how often, how much, and what else it touches. Answer what feels comfortable.</p>
        </div>

        <form id="mental-details-form" action="{{ route('mental.mode') }}" method="GET" class="space-y-8">
            {{-- Pass earlier answers through --}}
            @foreach((array) request('concern', []) as $c)
            <input type="hidden" name="concern[]" value="{{ $c }}">
            @endforeach
            <input type="hidden" name="why_thinking" value="{{ request('why_thinking') }}">
            <input type="hidden" name="duration" value="{{ request('duration') }}">
            <input type="hidden" name="triggers" value="{{ request('triggers') }}">

            {{-- Frequency --}}
            <div class="animate-fade-in" style="animation-delay: 0.1s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How often do these feelings or thoughts show up?</label>
                <div class="grid grid-cols-2 gap-2">
                    @php
                    $frequencies = [
                        ['value' => 'constant', 'label' => 'Most of the time', 'desc' => 'They\'re there all day'],
                        ['value' => 'daily', 'label' => 'Every day', 'desc' => 'Daily, but not all day'],
                        ['value' => 'weekly', 'label' => 'Several times a week', 'desc' => 'Comes in waves through the week'],
                        ['value' => 'occasionally', 'label' => 'Once in a while', 'desc' => 'Occasional, not constant'],
                    ];
                    @endphp
                    @foreach($frequencies as $f)
                    <label class="block cursor-pointer">
                        <input type="radio" name="frequency" value="{{ $f['value'] }}" class="peer hidden" required>
                        <div class="p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                            <div class="text-sm font-medium text-warm-700 peer-checked:text-mental-700">{{ $f['label'] }}</div>
                            <div class="text-xs text-warm-400 mt-0.5">{{ $f['desc'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Daily impact --}}
            <div class="animate-fade-in" style="animation-delay: 0.2s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How is it affecting your daily life? <span class="text-warm-400 font-normal">(pick any that apply)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $impacts = [
                        ['value' => 'sleep', 'label' => 'Sleep', 'desc' => 'Trouble falling/staying asleep, or sleeping too much'],
                        ['value' => 'work-school', 'label' => 'Work or school', 'desc' => 'Hard to focus, keep up, or show up'],
                        ['value' => 'appetite', 'label' => 'Appetite', 'desc' => 'Eating too little or too much'],
                        ['value' => 'social', 'label' => 'Social life', 'desc' => 'Avoiding people or canceling plans'],
                        ['value' => 'motivation', 'label' => 'Motivation', 'desc' => 'Hard to start or finish things'],
                        ['value' => 'none', 'label' => 'Not much yet', 'desc' => 'Not really getting in the way yet'],
                    ];
                    @endphp
                    @foreach($impacts as $i)
                    <label class="block cursor-pointer">
                        <input type="checkbox" name="impact[]" value="{{ $i['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-mental-200 peer-checked:border-mental-500 shrink-0 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-warm-700">{{ $i['label'] }}</div>
                                <div class="text-xs text-warm-400">{{ $i['desc'] }}</div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Physical signs --}}
            <div class="animate-fade-in" style="animation-delay: 0.3s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">Any physical signs that come with it? <span class="text-warm-400 font-normal">(pick any that apply)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $signs = [
                        ['value' => 'racing-heart', 'label' => 'Racing heart', 'tip' => 'Heart pounding or racing, even at rest'],
                        ['value' => 'chest-tightness', 'label' => 'Tight chest', 'tip' => 'Chest feels tight, heavy, or hard to breathe around'],
                        ['value' => 'headaches', 'label' => 'Headaches', 'tip' => 'Tension headaches or head pressure'],
                        ['value' => 'stomach', 'label' => 'Stomach issues', 'tip' => 'Nausea, butterflies, cramps, or an upset stomach'],
                        ['value' => 'trembling', 'label' => 'Shaking or trembling', 'tip' => 'Hands or body shaking when it flares up'],
                        ['value' => 'sweating', 'label' => 'Sweating', 'tip' => 'Sweating more than usual, especially with stress'],
                        ['value' => 'fatigue', 'label' => 'Heavy fatigue', 'tip' => 'Feeling bone-tired even after rest'],
                        ['value' => 'none', 'label' => 'No physical signs', 'tip' => 'Mostly mental/emotional, not physical'],
                    ];
                    @endphp
                    @foreach($signs as $s)
                    <label class="group block cursor-pointer relative">
                        <input type="checkbox" name="physical_signs[]" value="{{ $s['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white text-sm font-medium text-warm-600 peer-checked:border-mental-400 peer-checked:bg-mental-50 peer-checked:text-mental-700 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-mental-200 peer-checked:border-mental-500 shrink-0 flex items-center justify-center transition-colors group-hover:border-mental-400">
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
                <div id="tip-tooltip" class="hidden mt-2 bg-mental-50 border border-mental-200 rounded-xl p-3 text-sm text-mental-700 animate-fade-in">
                    <div class="flex gap-2">
                        <svg class="w-4 h-4 text-mental-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="tip-text"></span>
                    </div>
                </div>
            </div>

            {{-- Talked to anyone --}}
            <div class="animate-fade-in" style="animation-delay: 0.4s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">Have you talked to anyone about this?</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $talked = [
                        ['value' => 'no-one', 'label' => 'No one yet', 'desc' => 'I haven\'t brought it up'],
                        ['value' => 'friends', 'label' => 'Friends or family', 'desc' => 'People close to me know'],
                        ['value' => 'professional', 'label' => 'A therapist or counselor', 'desc' => 'Already seeing or seen a professional'],
                        ['value' => 'doctor', 'label' => 'A doctor', 'desc' => 'Mentioned it to a doctor'],
                        ['value' => 'online', 'label' => 'Online communities', 'desc' => 'Talked about it online or read others\' experiences'],
                    ];
                    @endphp
                    @foreach($talked as $t)
                    <label class="block cursor-pointer">
                        <input type="radio" name="talked_to" value="{{ $t['value'] }}" class="peer hidden" required>
                        <div class="p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                            <div class="text-sm font-medium text-warm-700 peer-checked:text-mental-700">{{ $t['label'] }}</div>
                            <div class="text-xs text-warm-400 mt-0.5">{{ $t['desc'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- What they've tried --}}
            <div class="animate-fade-in" style="animation-delay: 0.5s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">What have you tried so far? <span class="text-warm-400 font-normal">(pick any that apply)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $tried = [
                        ['value' => 'nothing', 'label' => 'Nothing yet'],
                        ['value' => 'self-care', 'label' => 'Self-care', 'desc' => 'Rest, hobbies, exercise, routines'],
                        ['value' => 'talking', 'label' => 'Talking it out', 'desc' => 'With someone I trust'],
                        ['value' => 'therapy', 'label' => 'Therapy or counseling'],
                        ['value' => 'medication', 'label' => 'Medication'],
                        ['value' => 'online', 'label' => 'Reading up on it', 'desc' => 'Looking things up, videos, forums'],
                    ];
                    @endphp
                    @foreach($tried as $t)
                    <label class="block cursor-pointer">
                        <input type="checkbox" name="tried[]" value="{{ $t['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-mental-200 peer-checked:border-mental-500 shrink-0 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-warm-700">{{ $t['label'] }}</div>
                                @if(isset($t['desc']))<div class="text-xs text-warm-400">{{ $t['desc'] }}</div>@endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Interference scale --}}
            <div class="animate-fade-in" style="animation-delay: 0.6s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How much does it get in the way of things you want to do?</label>
                <div class="flex gap-2">
                    @php
                    $interference = [
                        ['value' => '1', 'label' => 'Slightly', 'color' => 'bg-success/30'],
                        ['value' => '2', 'label' => 'A little', 'color' => 'bg-success/50'],
                        ['value' => '3', 'label' => 'Noticeably', 'color' => 'bg-warning/50'],
                        ['value' => '4', 'label' => 'A lot', 'color' => 'bg-warning/70'],
                        ['value' => '5', 'label' => 'It stops me', 'color' => 'bg-danger/50'],
                    ];
                    @endphp
                    @foreach($interference as $i)
                    <label class="flex-1 block cursor-pointer">
                        <input type="radio" name="interference" value="{{ $i['value'] }}" class="peer hidden" required>
                        <div class="p-2 sm:p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-6 h-6 rounded-full mx-auto mb-1 {{ $i['color'] }}"></div>
                            <div class="text-xs font-medium text-warm-600 peer-checked:text-mental-700">{{ $i['label'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-6 rounded-xl bg-mental-400 hover:bg-mental-500 text-white font-semibold transition-colors duration-200">How would you like help?</button>
            </div>
        </form>
    </div>
</div>

<script>
    const tipTooltip = document.getElementById('tip-tooltip');
    const tipText = document.getElementById('tip-text');

    document.querySelectorAll('.tip-trigger').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const tip = btn.getAttribute('data-tip');
            tipText.textContent = tip;
            tipTooltip.classList.remove('hidden');
            tipTooltip.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
</script>
@endsection