@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6 no-print">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to dashboard
    </a>

    @include('components.download-results', ['title' => $screening->title . ' — Screening Results'])

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6 animate-fade-in">
        <div class="w-12 h-12 rounded-xl
            @if($screening->type === 'physical') bg-physical-100 border border-physical-200
            @elseif($screening->type === 'mental') bg-mental-100 border border-mental-200
            @else bg-other-100 border border-other-200
            @endif flex items-center justify-center">
            @if($screening->type === 'physical')
                <svg class="w-6 h-6 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            @elseif($screening->type === 'mental')
                <svg class="w-6 h-6 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            @else
                <svg class="w-6 h-6 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div>
            <h1 class="font-display font-bold text-2xl text-warm-800">{{ $screening->title }}</h1>
            <p class="text-xs text-warm-400">{{ ucfirst($screening->type) }} screening &middot; {{ $screening->created_at->format('M j, Y g:i A') }} &middot; {{ $screening->created_at->diffForHumans() }}</p>
        </div>
    </div>

    {{-- Severity gauge --}}
    @if($screening->severity)
    <div class="bg-white rounded-2xl border border-warm-200 p-6 mb-6 animate-fade-in" style="animation-delay: 0.05s;">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Circular gauge --}}
            <div class="relative w-28 h-28 shrink-0">
                <svg class="w-28 h-28 -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="currentColor" stroke-width="8"
                        class="text-warm-100" />
                    <circle cx="50" cy="50" r="42" fill="none" stroke-width="8" stroke-linecap="round"
                        stroke-dasharray="264"
                        stroke-dashoffset="{{ 264 - (264 * $screening->severity / 5) }}"
                        class="transition-all duration-1000
                            @if($screening->severity <= 2) text-success
                            @elseif($screening->severity <= 3) text-warning
                            @else text-danger
                            @endif" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-warm-800">{{ $screening->severity }}</span>
                    <span class="text-[10px] text-warm-400">/ 5</span>
                </div>
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h3 class="font-semibold text-warm-800 mb-1">
                    @if($screening->severity <= 2) Mild concern
                    @elseif($screening->severity <= 3) Moderate concern
                    @else Significant concern
                    @endif
                </h3>
                <p class="text-sm text-warm-500 leading-relaxed">
                    @if($screening->severity <= 2)
                        Your responses suggest this is relatively mild. Continue monitoring and consider professional advice if it persists.
                    @elseif($screening->severity <= 3)
                        Your responses indicate a moderate level of concern. It may be worth discussing this with a healthcare provider.
                    @else
                        Your responses suggest this warrants professional attention. Please consider reaching out to a qualified provider soon.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content: Reported details --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-warm-200 shadow-sm p-6 animate-fade-in" style="animation-delay: 0.1s;">
                <h2 class="font-semibold text-warm-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Reported details
                </h2>
                <div class="space-y-3 text-sm">
                    @foreach($screening->data as $key => $value)
                        <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-3 py-2 border-b border-warm-100 last:border-0 last:pb-0">
                            <span class="font-medium text-warm-600 sm:w-44 shrink-0 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                            <span class="text-warm-500">
                                @if(is_array($value))
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($value as $v)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                @if($screening->type === 'physical') bg-physical-50 text-physical-700 border border-physical-200
                                                @elseif($screening->type === 'mental') bg-mental-50 text-mental-700 border border-mental-200
                                                @else bg-other-50 text-other-700 border border-other-200
                                                @endif
                                            ">{{ ucfirst(str_replace('-', ' ', $v)) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Assessment if available --}}
            @if($screening->assessment)
            <div class="bg-white rounded-2xl border border-warm-200 shadow-sm p-6 mt-6 animate-fade-in" style="animation-delay: 0.15s;">
                <h2 class="font-semibold text-warm-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Assessment summary
                </h2>
                <p class="text-sm text-warm-600 leading-relaxed">{{ $screening->assessment }}</p>
            </div>
            @endif
        </div>

        {{-- Sidebar: Metadata --}}
        <div class="space-y-6">
            {{-- Timestamp card --}}
            <div class="bg-white rounded-2xl border border-warm-200 p-5 animate-fade-in" style="animation-delay: 0.2s;">
                <h3 class="text-xs font-semibold text-warm-500 uppercase tracking-wider mb-3">Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-mental-400 mt-1.5 shrink-0"></div>
                        <div>
                            <div class="text-sm font-medium text-warm-700">Screening taken</div>
                            <div class="text-xs text-warm-400">{{ $screening->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                    @if($screening->created_at->diffInDays(now()) > 0)
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-warm-300 mt-1.5 shrink-0"></div>
                        <div>
                            <div class="text-sm text-warm-500">{{ $screening->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Related screenings --}}
            @php
                $related = \App\Models\Screening::where('type', $screening->type)
                    ->where('id', '!=', $screening->id)
                    ->when(auth()->check(), fn($q) => $q->where('user_id', auth()->id()))
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            @if($related->count() > 0)
            <div class="bg-white rounded-2xl border border-warm-200 p-5 animate-fade-in" style="animation-delay: 0.25s;">
                <h3 class="text-xs font-semibold text-warm-500 uppercase tracking-wider mb-3">Related screenings</h3>
                <div class="space-y-2">
                    @foreach($related as $r)
                    <a href="{{ route('screenings.show', $r) }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-warm-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                            @if($r->type === 'physical') bg-physical-100
                            @elseif($r->type === 'mental') bg-mental-100
                            @else bg-other-100
                            @endif">
                            @if($r->severity)
                                <span class="text-[10px] font-bold
                                    @if($r->severity <= 2) text-success
                                    @elseif($r->severity <= 3) text-warning
                                    @else text-danger
                                    @endif">{{ $r->severity }}</span>
                            @else
                                <svg class="w-3.5 h-3.5 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-warm-700 truncate group-hover:text-warm-800">{{ $r->title }}</div>
                            <div class="text-[10px] text-warm-400">{{ $r->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Disclaimer --}}
            <div class="bg-mental-50 border border-mental-200 rounded-2xl p-4 animate-fade-in" style="animation-delay: 0.3s;">
                <p class="text-xs text-mental-600 leading-relaxed">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    This is a preliminary screening tool, not a diagnosis. Always consult a qualified healthcare professional.
                </p>
            </div>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="no-print mt-8 flex flex-col sm:flex-row gap-3 animate-fade-in" style="animation-delay: 0.35s;">
        <a href="{{ url('/dashboard') }}" class="flex-1 py-3 px-6 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold text-center hover:bg-warm-50 transition-colors">
            Back to dashboard
        </a>
        <a href="{{ route(strtolower($screening->type) . '.index') }}" class="flex-1 py-3 px-6 rounded-xl bg-warm-800 hover:bg-warm-700 text-white font-semibold text-center transition-colors">
            Start new screening
        </a>
    </div>
</div>
@endsection

@section('footer')
@include('partials.footer')
@endsection
