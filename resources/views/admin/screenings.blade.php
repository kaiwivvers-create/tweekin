@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Screenings</h1>
        <p class="text-sm text-warm-500">View all screenings across the platform.</p>
    </div>

    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-warm-200 bg-warm-50">
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Type</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Title</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">User</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Severity</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-100">
                    @forelse($screenings as $screening)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                @if($screening->type === 'physical') bg-physical-100 text-physical-600
                                @elseif($screening->type === 'mental') bg-mental-100 text-mental-600
                                @else bg-other-100 text-other-600
                                @endif">{{ ucfirst($screening->type) }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium text-warm-800">{{ $screening->title }}</td>
                        <td class="px-6 py-4 text-warm-500">{{ $screening->user->name ?? 'Guest' }}</td>
                        <td class="px-6 py-4">
                            @if($screening->severity)
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                                    @if($screening->severity <= 2) bg-success/20 text-success
                                    @elseif($screening->severity <= 3) bg-warning/20 text-warning
                                    @else bg-danger/20 text-danger
                                    @endif">{{ $screening->severity }}/5</span>
                            @else
                                <span class="text-warm-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-warm-400 text-xs">{{ $screening->created_at->format('M j, Y g:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-warm-400">No screenings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-warm-100">
            {{ $screenings->links() }}
        </div>
    </div>
</div>
@endsection
