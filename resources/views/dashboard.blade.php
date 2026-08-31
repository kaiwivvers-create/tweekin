@extends('layouts.app')

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-8 sm:py-10">
    <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Welcome back, {{ Auth::user()->name }}</h1>
    <p class="text-warm-500 mb-8">Here's an overview of your screening activity.</p>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ $counts['total'] }}</div>
            <div class="text-sm text-warm-500">Total screenings</div>
        </div>
        <div class="bg-white rounded-2xl border border-physical-200 p-5">
            <div class="text-2xl font-bold text-physical-600">{{ $counts['physical'] }}</div>
            <div class="text-sm text-warm-500">Physical</div>
        </div>
        <div class="bg-white rounded-2xl border border-mental-200 p-5">
            <div class="text-2xl font-bold text-mental-600">{{ $counts['mental'] }}</div>
            <div class="text-sm text-warm-500">Mental</div>
        </div>
        <div class="bg-white rounded-2xl border border-other-200 p-5">
            <div class="text-2xl font-bold text-other-600">{{ $counts['other'] }}</div>
            <div class="text-sm text-warm-500">Other</div>
        </div>
    </div>

    {{-- Weekly activity --}}
    @php
        $weeklyData = $screenings->filter(function ($s) {
            return $s->created_at->isAfter(now()->subWeek());
        })->groupBy(function ($s) {
            return $s->created_at->format('D');
        });
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $maxWeek = max(array_merge([1], array_map(fn($d) => $weeklyData[$d]->count(), array_filter($days, fn($d) => isset($weeklyData[$d])))));
    @endphp

    @if($counts['total'] > 0)
    <div class="bg-white rounded-2xl border border-warm-200 p-6 mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-bold text-lg text-warm-800">This week</h2>
            @php
                $thisWeekCount = $screenings->filter(fn($s) => $s->created_at->isAfter(now()->subWeek()))->count();
                $lastWeekCount = $screenings->filter(fn($s) => $s->created_at->isBetween(now()->subWeeks(2), now()->subWeek()))->count();
            @endphp
            @if($lastWeekCount > 0)
                @php $trend = round((($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100); @endphp
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $trend >= 0 ? 'bg-physical-100 text-physical-700' : 'bg-mental-100 text-mental-700' }}">
                    {{ $trend >= 0 ? '+' : '' }}{{ $trend }}% vs last week
                </span>
            @endif
        </div>
        <div class="flex items-end gap-2 h-28">
            @foreach($days as $day)
                @php $count = isset($weeklyData[$day]) ? $weeklyData[$day]->count() : 0; @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full rounded-lg transition-all {{ $count > 0 ? 'bg-gradient-to-t from-mental-400 to-mental-300' : 'bg-warm-100' }}"
                         style="height: {{ $count > 0 ? max(8, ($count / $maxWeek) * 100) : 4 }}%"></div>
                    <span class="text-[10px] font-medium text-warm-400">{{ substr($day, 0, 1) }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick actions --}}
    <h2 class="font-display font-bold text-xl text-warm-800 mb-4">Start a new screening</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
        <a href="{{ route('physical.index') }}" class="group flex items-center gap-4 bg-white rounded-2xl border-2 border-physical-200 p-5 card-hover">
            <div class="w-12 h-12 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-warm-800">Physical</div>
                <div class="text-sm text-warm-500">Start a new check</div>
            </div>
        </a>
        <a href="{{ route('mental.index') }}" class="group flex items-center gap-4 bg-white rounded-2xl border-2 border-mental-200 p-5 card-hover">
            <div class="w-12 h-12 rounded-xl bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-warm-800">Mental</div>
                <div class="text-sm text-warm-500">Check in on feelings</div>
            </div>
        </a>
        <a href="{{ route('other.index') }}" class="group flex items-center gap-4 bg-white rounded-2xl border-2 border-other-200 p-5 card-hover">
            <div class="w-12 h-12 rounded-xl bg-other-100 border border-other-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="font-semibold text-warm-800">Not sure?</div>
                <div class="text-sm text-warm-500">Figure it out together</div>
            </div>
        </a>
    </div>

    {{-- Screening history --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-display font-bold text-xl text-warm-800">Recent screenings</h2>
        @if($screenings->count() > 5)
            <span class="text-xs text-warm-400">{{ $screenings->count() }} total</span>
        @endif
    </div>

    @if($screenings->count() > 0)
        <div class="space-y-3">
            @foreach($screenings as $s)
            <a href="{{ route('screenings.show', $s) }}" class="group flex items-center gap-4 bg-white rounded-2xl border border-warm-200 p-4 card-hover">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                    @if($s->type === 'physical') bg-physical-100 border border-physical-200
                    @elseif($s->type === 'mental') bg-mental-100 border border-mental-200
                    @else bg-other-100 border border-other-200
                    @endif">
                    @if($s->type === 'physical')
                        <svg class="w-5 h-5 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    @elseif($s->type === 'mental')
                        <svg class="w-5 h-5 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-warm-800 truncate">{{ $s->title }}</div>
                    <div class="text-xs text-warm-400">{{ ucfirst($s->type) }} &middot; {{ $s->created_at->diffForHumans() }}</div>
                </div>
                @if($s->severity)
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold shrink-0
                        @if($s->severity <= 2) bg-success/20 text-success
                        @elseif($s->severity <= 3) bg-warning/20 text-warning
                        @else bg-danger/20 text-danger
                        @endif
                    ">{{ $s->severity }}/5</span>
                @endif
                <svg class="w-4 h-4 text-warm-400 group-hover:text-warm-600 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-warm-200 p-8 text-center">
            <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-warm-500 text-sm mb-4">No screenings yet. Start one above to see your history here.</p>
            <a href="{{ route('physical.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-physical-600 hover:text-physical-700 transition-colors">
                Start your first screening
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    @endif

    {{-- Wellness tip --}}
    <div class="mt-10 bg-gradient-to-br from-mental-50 to-physical-50 rounded-2xl border border-mental-200/50 p-6">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-mental-200 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-warm-800 text-sm mb-1">Wellness tip</h3>
                <p class="text-sm text-warm-600 leading-relaxed">Regular check-ins with yourself — even quick ones — can help you notice patterns you might otherwise miss. You're already doing that by being here.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
@include('partials.footer')
@endsection
