@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-8">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to dashboard
    </a>

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl
            @if($screening->type === 'physical') bg-physical-100 border border-physical-200
            @elseif($screening->type === 'mental') bg-mental-100 border border-mental-200
            @else bg-other-100 border border-other-200
            @endif flex items-center justify-center">
            @if($screening->type === 'physical')
                <svg class="w-5 h-5 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            @elseif($screening->type === 'mental')
                <svg class="w-5 h-5 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            @else
                <svg class="w-5 h-5 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div>
            <h1 class="font-display font-bold text-2xl text-warm-800">{{ $screening->title }}</h1>
            <p class="text-xs text-warm-400">{{ ucfirst($screening->type) }} screening &middot; {{ $screening->created_at->format('M j, Y g:i A') }}</p>
        </div>
    </div>

    @if($screening->severity)
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-sm font-semibold text-warm-700">Severity:</span>
            <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                @if($screening->severity <= 2) bg-success/20 text-success
                @elseif($screening->severity <= 3) bg-warning/20 text-warning
                @else bg-danger/20 text-danger
                @endif
            ">{{ $screening->severity }} / 5</span>
        </div>
        <div class="h-2 bg-warm-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full
                @if($screening->severity <= 2) bg-success
                @elseif($screening->severity <= 3) bg-warning
                @else bg-danger
                @endif
            " style="width: {{ $screening->severity * 20 }}%"></div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-warm-200 shadow-sm p-6">
        <h2 class="font-semibold text-warm-800 mb-4">Reported details</h2>
        <div class="space-y-3 text-sm">
            @foreach($screening->data as $key => $value)
                <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-3">
                    <span class="font-medium text-warm-600 sm:w-40 shrink-0">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                    <span class="text-warm-500">
                        @if(is_array($value))
                            {{ implode(', ', array_map(fn($v) => ucfirst(str_replace('-', ' ', $v)), $value)) }}
                        @else
                            {{ $value }}
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ url('/dashboard') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold text-center hover:bg-warm-50 transition-colors">
            Back to dashboard
        </a>
        <button onclick="window.print()" class="flex-1 py-3 px-6 rounded-xl bg-warm-800 hover:bg-warm-700 text-white font-semibold transition-colors">
            Print
        </button>
    </div>
</div>
@endsection

@section('footer')
@include('partials.footer')
@endsection
