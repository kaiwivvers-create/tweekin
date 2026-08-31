{{-- Vertical progress indicator --}}
{{-- Usage: @include('components.vertical-progress', ['steps' => [...], 'current' => 1, 'theme' => 'physical|mental|other']) --}}

@php
    $themeClass = match($theme ?? 'physical') {
        'mental' => [
            'active' => 'bg-mental-400',
            'activeRing' => 'ring-mental-100',
            'line' => 'bg-mental-300',
            'done' => 'bg-mental-400',
            'doneText' => 'text-mental-400',
            'text' => 'text-mental-600',
            'dot' => 'border-mental-200',
            'dotText' => 'text-mental-400',
            'connector' => 'bg-mental-100',
        ],
        'other' => [
            'active' => 'bg-other-400',
            'activeRing' => 'ring-other-100',
            'line' => 'bg-other-300',
            'done' => 'bg-other-400',
            'doneText' => 'text-other-400',
            'text' => 'text-other-600',
            'dot' => 'border-other-200',
            'dotText' => 'text-other-400',
            'connector' => 'bg-other-100',
        ],
        default => [
            'active' => 'bg-physical-400',
            'activeRing' => 'ring-physical-100',
            'line' => 'bg-physical-300',
            'done' => 'bg-physical-400',
            'doneText' => 'text-physical-400',
            'text' => 'text-physical-600',
            'dot' => 'border-physical-200',
            'dotText' => 'text-physical-400',
            'connector' => 'bg-physical-100',
        ],
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
                @elseif($isActive) {{ $themeClass['active'] }} ring-4 {{ $themeClass['activeRing'] }}
                @else bg-white border-2 {{ $themeClass['dot'] }}
                @endif
            ">
                @if($isDone)
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                @else
                    <span class="text-xs font-bold {{ $isActive ? 'text-white' : $themeClass['dotText'] }}">{{ $stepNum }}</span>
                @endif
            </div>

            {{-- Label (only show on active or done) --}}
            @if($isActive || $isDone)
                <span class="text-[10px] font-medium {{ $isDone ? $themeClass['doneText'] : $themeClass['text'] }} mt-1 whitespace-nowrap">{{ $step }}</span>
            @endif
        </div>

        {{-- Connector line --}}
        @if(!$loop->last)
            <div class="w-0.5 h-8 {{ $isDone ? $themeClass['line'] : $themeClass['connector'] }} transition-colors duration-300"></div>
        @endif
    @endforeach
</div>
