@extends('layouts.app')

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-8 sm:py-10">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-1">Screening comparison</h1>
                <p class="text-warm-500 text-sm">Comparing {{ $screenings->count() }} screenings side by side</p>
            </div>
            <a href="{{ route('compare.index') }}" class="text-sm font-medium text-mental-600 hover:text-mental-700 transition-colors">
                &larr; Back to selection
            </a>
        </div>

        {{-- Severity comparison bar --}}
        @if($screenings->whereNotNull('severity')->count() > 0)
        <div class="bg-white rounded-2xl border border-warm-200 p-6 mb-8 animate-fade-in">
            <h2 class="font-display font-bold text-lg text-warm-800 mb-4">Severity over time</h2>
            <div class="space-y-4">
                @foreach($screenings->sortByDesc('created_at') as $s)
                    @if($s->severity)
                    <div class="flex items-center gap-4">
                        <div class="w-24 text-right shrink-0">
                            <span class="text-xs text-warm-400">{{ $s->created_at->format('M j') }}</span>
                        </div>
                        <div class="flex-1 h-8 bg-warm-100 rounded-lg overflow-hidden relative">
                            <div class="h-full rounded-lg transition-all duration-700 ease-out
                                @if($s->severity <= 2) bg-success/60
                                @elseif($s->severity <= 3) bg-warning/60
                                @else bg-danger/60
                                @endif"
                                style="width: {{ ($s->severity / 5) * 100 }}%">
                            </div>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-semibold text-warm-700">{{ $s->severity }}/5</span>
                        </div>
                        <div class="w-32 shrink-0">
                            <span class="text-xs font-medium text-warm-600 truncate block">{{ Str::limit($s->title, 25) }}</span>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @php
                $severities = $screenings->whereNotNull('severity')->pluck('severity');
                $first = $severities->first();
                $last = $severities->last();
                $trend = $last <=> $first;
            @endphp
            @if($severities->count() >= 2)
            <div class="mt-4 pt-4 border-t border-warm-100 flex items-center gap-2">
                <span class="text-xs text-warm-400">Overall trend:</span>
                @if($trend < 0)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-success/20 text-success">Improving</span>
                @elseif($trend > 0)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-danger/20 text-danger">Worsening</span>
                @else
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-warning/20 text-warning">Stable</span>
                @endif
            </div>
            @endif
        </div>
        @endif

        {{-- Side-by-side cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($screenings->count(), 3) }} gap-4 mb-8">
            @foreach($screenings as $s)
            <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden animate-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                {{-- Card header --}}
                <div class="p-5 border-b border-warm-100
                    @if($s->type === 'physical') bg-physical-50/50
                    @elseif($s->type === 'mental') bg-mental-50/50
                    @else bg-other-50/50
                    @endif">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center
                            @if($s->type === 'physical') bg-physical-100 border border-physical-200
                            @elseif($s->type === 'mental') bg-mental-100 border border-mental-200
                            @else bg-other-100 border border-other-200
                            @endif">
                            @if($s->type === 'physical')
                                <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            @elseif($s->type === 'mental')
                                <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div>
                            <div class="font-semibold text-warm-800 text-sm">{{ $s->title }}</div>
                            <div class="text-xs text-warm-400">{{ $s->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                    @if($s->severity)
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex-1 h-2 bg-warm-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full
                                @if($s->severity <= 2) bg-success
                                @elseif($s->severity <= 3) bg-warning
                                @else bg-danger
                                @endif"
                                style="width: {{ ($s->severity / 5) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-semibold text-warm-600">{{ $s->severity }}/5</span>
                    </div>
                    @endif
                </div>

                {{-- Card data --}}
                <div class="p-5 space-y-3">
                    @if($s->assessment)
                    <div>
                        <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Assessment</span>
                        <p class="text-sm text-warm-600 mt-0.5">{{ $s->assessment }}</p>
                    </div>
                    @endif

                    @if($s->data && is_array($s->data))
                        @foreach($s->data as $key => $value)
                            @if($value && $key !== 'additional_info')
                            <div>
                                <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">{{ str_replace('_', ' ', ucfirst($key)) }}</span>
                                <div class="mt-0.5">
                                    @if(is_array($value))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($value as $v)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-600">{{ ucfirst(str_replace('-', ' ', $v)) }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-warm-600">{{ ucfirst(str_replace('-', ' ', $value)) }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endif

                    @if($s->data['additional_info'] ?? '')
                    <div>
                        <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Additional notes</span>
                        <p class="text-sm text-warm-600 mt-0.5">{{ $s->data['additional_info'] }}</p>
                    </div>
                    @endif
                </div>

                {{-- Card footer --}}
                <div class="px-5 py-3 border-t border-warm-100 bg-warm-50/50">
                    <a href="{{ route('screenings.show', $s) }}" class="text-xs font-medium text-mental-600 hover:text-mental-700 transition-colors">
                        View full details &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Insights --}}
        @if($screenings->count() >= 2)
        <div class="bg-gradient-to-br from-mental-50 to-other-50 rounded-2xl border border-mental-200/50 p-6 animate-fade-in" style="animation-delay: 0.3s;">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-mental-200 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <div>
                    <h3 class="font-display font-bold text-warm-800 text-sm mb-1">Comparison insights</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">
                        You've compared <strong>{{ $screenings->count() }} screenings</strong> of
                        <strong>{{ $screenings->pluck('type')->unique()->implode(', ') }}</strong> type(s).
                        @if($screenings->whereNotNull('severity')->count() >= 2)
                            Severity ranged from <strong>{{ $screenings->whereNotNull('severity')->min('severity') }}</strong>
                            to <strong>{{ $screenings->whereNotNull('severity')->max('severity') }}</strong> out of 5.
                        @endif
                        Tracking patterns over time can help you and a professional understand what's changing.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('footer')
@include('partials.footer')
@endsection
