@extends('layouts.app')

@section('content')
<div class="w-full flex">
    {{-- Sidebar with vertical progress --}}
    <div class="hidden lg:flex w-24 shrink-0 border-r border-physical-100 bg-physical-50/30 py-12 justify-center sticky top-14 h-[calc(100vh-3.5rem)]">
        @include('components.vertical-progress', ['steps' => ['Category', 'Questions', 'Details', 'Results'], 'current' => 3, 'theme' => 'physical'])
    </div>

    <div class="flex-1 px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
        <a href="{{ route('physical.questions') }}?{{ http_build_query(request()->except('frequency', 'impact', 'tried', 'changes', 'better_worse')) }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        <div class="lg:hidden mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-physical-600">Physical symptoms</span>
                <span class="text-xs text-warm-400">Step 3 of 4</span>
            </div>
            <div class="h-1.5 bg-physical-100 rounded-full overflow-hidden">
                <div class="h-full bg-physical-400 rounded-full progress-bar" style="width: 75%"></div>
            </div>
        </div>

        <div class="mb-8 animate-fade-in">
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">A few more questions</h1>
            <p class="text-warm-500 leading-relaxed">The more context you give, the more useful your assessment will be. None of these are required to be perfect — answer what you can.</p>
        </div>

        <form id="physical-details-form" action="{{ route('physical.results') }}" method="GET" class="space-y-8">
            {{-- Pass earlier answers through --}}
            @foreach((array) request('symptom', []) as $s)
            <input type="hidden" name="symptom[]" value="{{ $s }}">
            @endforeach
            @foreach((array) request('present_symptoms', []) as $s)
            <input type="hidden" name="present_symptoms[]" value="{{ $s }}">
            @endforeach
            <input type="hidden" name="duration" value="{{ request('duration') }}">
            <input type="hidden" name="severity" value="{{ request('severity') }}">
            <input type="hidden" name="additional_info" value="{{ request('additional_info') }}">

            {{-- Frequency --}}
            <div class="animate-fade-in" style="animation-delay: 0.1s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How often do you notice it?</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $frequencies = [
                        ['value' => 'constant', 'label' => 'It\'s there all the time', 'desc' => 'Constant or near-constant'],
                        ['value' => 'comes-goes', 'label' => 'It comes and goes', 'desc' => 'Comes and goes throughout the day'],
                        ['value' => 'flares', 'label' => 'It flares up sometimes', 'desc' => 'Mostly fine, but flares up now and then'],
                        ['value' => 'once', 'label' => 'It happened once', 'desc' => 'Happened once and hasn\'t come back'],
                    ];
                    @endphp
                    @foreach($frequencies as $f)
                    <label class="block cursor-pointer">
                        <input type="radio" name="frequency" value="{{ $f['value'] }}" class="peer hidden" required>
                        <div class="p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 transition-all duration-200">
                            <div class="text-sm font-medium text-warm-700 peer-checked:text-physical-700">{{ $f['label'] }}</div>
                            <div class="text-xs text-warm-400 mt-0.5">{{ $f['desc'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Daily impact --}}
            <div class="animate-fade-in" style="animation-delay: 0.2s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How much is it affecting your daily life? <span class="text-warm-400 font-normal">(pick any that apply)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $impacts = [
                        ['value' => 'sleep', 'label' => 'Sleep', 'desc' => 'Hard to fall or stay asleep'],
                        ['value' => 'work-school', 'label' => 'Work or school', 'desc' => 'Hard to focus or keep up'],
                        ['value' => 'eating', 'label' => 'Eating', 'desc' => 'Affecting my appetite or eating'],
                        ['value' => 'movement', 'label' => 'Movement', 'desc' => 'Can\'t move, exercise, or do things like usual'],
                        ['value' => 'mood', 'label' => 'Mood', 'desc' => 'It\'s getting to my mood'],
                        ['value' => 'none', 'label' => 'Not much yet', 'desc' => 'Not really affecting anything yet'],
                    ];
                    @endphp
                    @foreach($impacts as $i)
                    <label class="block cursor-pointer">
                        <input type="checkbox" name="impact[]" value="{{ $i['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center">
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

            {{-- What makes it better/worse --}}
            <div class="animate-fade-in" style="animation-delay: 0.3s;">
                <label for="better-worse" class="block text-sm font-semibold text-warm-700 mb-2">What seems to make it better or worse? (optional)</label>
                <textarea id="better-worse" name="better_worse" rows="2" placeholder="e.g., 'It gets worse after meals', 'Resting and drinking water helps'..." class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 placeholder-warm-400 focus:border-physical-400 focus:ring-0 resize-none transition-colors"></textarea>
            </div>

            {{-- What they've tried --}}
            <div class="animate-fade-in" style="animation-delay: 0.4s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">Have you tried anything for it? <span class="text-warm-400 font-normal">(pick any that apply)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $tried = [
                        ['value' => 'nothing', 'label' => 'Nothing yet'],
                        ['value' => 'otc', 'label' => 'Over-the-counter meds', 'desc' => 'Pain relievers, antacids, cold meds, etc.'],
                        ['value' => 'home', 'label' => 'Home remedies', 'desc' => 'Rest, heat/cold, fluids, changes in routine'],
                        ['value' => 'doctor', 'label' => 'Already saw a doctor', 'desc' => 'Been evaluated or treated already'],
                        ['value' => 'alternative', 'label' => 'Alternative treatments', 'desc' => 'Supplements, massage, acupuncture, etc.'],
                    ];
                    @endphp
                    @foreach($tried as $t)
                    <label class="block cursor-pointer">
                        <input type="checkbox" name="tried[]" value="{{ $t['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center">
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

            {{-- Recent changes --}}
            <div class="animate-fade-in" style="animation-delay: 0.5s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">Any recent changes that might be related? <span class="text-warm-400 font-normal">(pick any that apply)</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                    $changes = [
                        ['value' => 'medication', 'label' => 'New medication', 'desc' => 'Started something new or changed a dose'],
                        ['value' => 'diet', 'label' => 'Diet change', 'desc' => 'Changed what or how I eat'],
                        ['value' => 'travel', 'label' => 'Recent travel', 'desc' => 'Traveled somewhere recently'],
                        ['value' => 'exposure', 'label' => 'Sick contacts', 'desc' => 'Been around someone who was sick'],
                        ['value' => 'stress', 'label' => 'Stressful period', 'desc' => 'A stressful event or stretch of time'],
                        ['value' => 'none', 'label' => 'Nothing stands out', 'desc' => 'No obvious changes'],
                    ];
                    @endphp
                    @foreach($changes as $c)
                    <label class="block cursor-pointer">
                        <input type="checkbox" name="changes[]" value="{{ $c['value'] }}" class="peer hidden">
                        <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 transition-all duration-200">
                            <div class="w-4 h-4 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-warm-700">{{ $c['label'] }}</div>
                                <div class="text-xs text-warm-400">{{ $c['desc'] }}</div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-6 rounded-xl bg-physical-400 hover:bg-physical-500 text-white font-semibold transition-colors duration-200">Get my assessment</button>
            </div>
        </form>
    </div>
</div>
@endsection