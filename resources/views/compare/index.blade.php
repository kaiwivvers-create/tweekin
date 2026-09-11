@extends('layouts.app')

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-8 sm:py-10">
    <div class="max-w-4xl mx-auto">
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Compare screenings</h1>
        <p class="text-warm-500 mb-8">Select two or more screenings to see how they differ side by side.</p>

        @if(session('error'))
            <div class="bg-danger/10 border border-danger/20 rounded-xl p-4 mb-6 text-sm text-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($screenings->count() < 2)
            <div class="bg-white rounded-2xl border border-warm-200 p-8 text-center">
                <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-warm-500 text-sm mb-4">You need at least 2 screenings to compare. Complete some screenings first!</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('physical.index') }}" class="text-sm font-medium text-physical-600 hover:text-physical-700">Physical screening</a>
                    <span class="text-warm-300">&middot;</span>
                    <a href="{{ route('mental.index') }}" class="text-sm font-medium text-mental-600 hover:text-mental-700">Mental screening</a>
                </div>
            </div>
        @else
            <form action="{{ route('compare.show') }}" method="POST" id="compare-form">
                @csrf
                <div class="space-y-3 mb-8">
                    @foreach($screenings as $s)
                    <label class="flex items-center gap-4 bg-white rounded-2xl border-2 border-warm-200 p-4 card-hover cursor-pointer transition-all duration-200 has-[:checked]:border-mental-400 has-[:checked]:bg-mental-50/50">
                        <input type="checkbox" name="screenings[]" value="{{ $s->id }}" class="w-5 h-5 rounded-lg border-2 border-warm-300 text-mental-500 focus:ring-mental-400 focus:ring-offset-0">
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
                    </label>
                    @endforeach
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-sm text-warm-400" id="selected-count">0 selected</p>
                    <button type="submit" id="compare-btn" disabled class="px-6 py-3 rounded-xl bg-mental-400 hover:bg-mental-500 text-white font-semibold transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        Compare selected
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="screenings[]"]');
    const countEl = document.getElementById('selected-count');
    const btn = document.getElementById('compare-btn');
    if (!checkboxes.length) return;

    function updateCount() {
        const checked = document.querySelectorAll('input[name="screenings[]"]:checked').length;
        countEl.textContent = checked + ' selected';
        btn.disabled = checked < 2;
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
});
</script>
@endsection

@section('footer')
@include('partials.footer')
@endsection
