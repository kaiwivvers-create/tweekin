@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Dashboard</h1>
        <p class="text-sm text-warm-500">Overview of your platform activity.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ $total_users }}</div>
            <div class="text-sm text-warm-500">Total users</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ $total_screenings }}</div>
            <div class="text-sm text-warm-500">Total screenings</div>
        </div>
        <div class="bg-white rounded-2xl border border-physical-200 p-5">
            <div class="text-2xl font-bold text-physical-600">{{ $physical_screenings }}</div>
            <div class="text-sm text-warm-500">Physical</div>
        </div>
        <div class="bg-white rounded-2xl border border-mental-200 p-5">
            <div class="text-2xl font-bold text-mental-600">{{ $mental_screenings }}</div>
            <div class="text-sm text-warm-500">Mental</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="bg-white rounded-2xl border border-warm-200 p-6">
            <h2 class="font-semibold text-warm-800 mb-4">Recent users</h2>
            <div class="space-y-3">
                @forelse($recent_users as $user)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-mental-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-warm-800 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-warm-400">{{ $user->email }}</div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-600">{{ $user->role->label ?? 'No role' }}</span>
                </div>
                @empty
                <p class="text-sm text-warm-400">No users yet.</p>
                @endforelse
            </div>
            <a href="{{ url('/admin/users') }}" class="inline-flex items-center gap-1 text-sm font-medium text-mental-600 hover:text-mental-700 mt-4">
                View all users
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Recent Screenings --}}
        <div class="bg-white rounded-2xl border border-warm-200 p-6">
            <h2 class="font-semibold text-warm-800 mb-4">Recent screenings</h2>
            <div class="space-y-3">
                @forelse($recent_screenings as $screening)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                        @if($screening->type === 'physical') bg-physical-100
                        @elseif($screening->type === 'mental') bg-mental-100
                        @else bg-other-100
                        @endif">
                        @if($screening->type === 'physical')
                            <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        @elseif($screening->type === 'mental')
                            <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-warm-800 truncate">{{ $screening->title }}</div>
                        <div class="text-xs text-warm-400">{{ $screening->user->name ?? 'Guest' }} &middot; {{ $screening->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-warm-400">No screenings yet.</p>
                @endforelse
            </div>
            <a href="{{ url('/admin/screenings') }}" class="inline-flex items-center gap-1 text-sm font-medium text-mental-600 hover:text-mental-700 mt-4">
                View all screenings
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
@endsection
