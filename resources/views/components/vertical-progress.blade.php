{{-- Vertical progress indicator --}}
{{-- Usage: @include('components.vertical-progress', ['steps' => [...], 'current' => 1, 'theme' => 'physical|mental|other']) --}}

@php
    $themeClass = match($theme ?? 'physical') {
        'mental' => ['active' => 'bg-mental-400', 'line' => 'bg-mental-300', 'done' => 'bg-mental-400', 'text' => 'text-mental-600'],
        'other' => ['active' => 'bg-other-400', 'line' => 'bg-other-300', 'done' => 'bg-other-400', 'text' => 'text-other-600'],
        default => ['active' => 'bg-physical-400', 'line' => 'bg-physical-300', 'done' => 'bg-physical-400', 'text' => 'text-physical-600'],
    };
@endphp

<div class="flex flex-col items-center gap-0">
    @foreach($steps as $index => $step)
        @php
            $stepNum = $index + 1;
            $isDone = $stepNum < $current;
            $isActive = $stepNum === $current;
        @endphp

        {{-- Step dot --}}
        <div class="flex flex-col items-center">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-all duration-300
                @if($isDone) {{ $themeClass['done'] }}
                @elseif($isActive) {{ $themeClass['active'] }} ring-4 ring-{{ $theme ?? 'physical' }}-100
                @else bg-warm-200
                @endif
            ">
                @if($isDone)
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                @else
                    <span class="text-xs font-bold {{ $isActive ? 'text-white' : 'text-warm-500' }}">{{ $stepNum }}</span>
                @endif
            </div>

            {{-- Label (only show on active or done) --}}
            @if($isActive || $isDone)
                <span class="text-[10px] font-medium {{ $isDone ? 'text-warm-400' : $themeClass['text'] }} mt-1 whitespace-nowrap">{{ $step }}</span>
            @endif
        </div>

        {{-- Connector line --}}
        @if(!$loop->last)
            <div class="w-0.5 h-8 {{ $isDone ? $themeClass['done'] : 'bg-warm-200' }} transition-colors duration-300"></div>
        @endif
    @endforeach
</div>
