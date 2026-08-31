@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Reports</h1>
        <p class="text-sm text-warm-500">Screening activity over the last 30 days.</p>
    </div>

    <div class="bg-white rounded-2xl border border-warm-200 p-6">
        <h2 class="font-semibold text-warm-800 mb-4">Daily screenings</h2>
        @if($daily->count() > 0)
            <div class="space-y-2">
                @foreach($daily->sortByDesc(function($item) { return $item->first()['date'] ?? ''; }) as $date => $entries)
                <div class="flex items-center gap-4 py-2 border-b border-warm-100 last:border-0">
                    <span class="text-sm text-warm-600 w-32 shrink-0">{{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</span>
                    <div class="flex gap-2 flex-wrap">
                        @foreach($entries as $entry)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                @if($entry->type === 'physical') bg-physical-100 text-physical-600
                                @elseif($entry->type === 'mental') bg-mental-100 text-mental-600
                                @else bg-other-100 text-other-600
                                @endif">{{ $entry->type }}: {{ $entry->count }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-warm-400 text-sm">No screening data in the last 30 days.</p>
        @endif
    </div>
</div>
@endsection
