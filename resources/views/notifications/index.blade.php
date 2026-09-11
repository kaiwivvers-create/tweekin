@extends('layouts.app')

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-8 sm:py-10">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-1">Notifications</h1>
                <p class="text-warm-500 text-sm">Stay updated on your screening activity</p>
            </div>
            @if($notifications->where('read', false)->count() > 0)
            <button onclick="markAllRead()" class="text-sm font-medium text-mental-600 hover:text-mental-700 transition-colors" id="mark-all-btn">
                Mark all read
            </button>
            @endif
        </div>

        @if($notifications->count() > 0)
            <div class="space-y-2">
                @foreach($notifications as $n)
                <div class="flex items-start gap-4 bg-white rounded-2xl border border-warm-200 p-4 transition-all duration-200 {{ $n->read ? 'opacity-60' : 'border-l-4 border-l-mental-400' }}" id="notif-{{ $n->id }}">
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                        @if($n->type === 'success') bg-success/15
                        @elseif($n->type === 'warning') bg-warning/15
                        @elseif($n->type === 'reminder') bg-mental-100
                        @else bg-warm-100
                        @endif">
                        @if($n->icon === 'heart')
                            <svg class="w-5 h-5 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        @elseif($n->icon === 'brain')
                            <svg class="w-5 h-5 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        @elseif($n->icon === 'clock')
                            <svg class="w-5 h-5 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($n->type === 'warning')
                            <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        @else
                            <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-warm-800 text-sm {{ $n->read ? '' : '' }}">{{ $n->title }}</h3>
                            <span class="text-[10px] text-warm-400 shrink-0">{{ $n->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-warm-500 leading-relaxed mt-0.5">{{ $n->message }}</p>
                        @if($n->action_url && $n->action_label)
                        <a href="{{ $n->action_url }}" onclick="markRead({{ $n->id }})" class="inline-block text-xs font-medium text-mental-600 hover:text-mental-700 transition-colors mt-2">
                            {{ $n->action_label }} &rarr;
                        </a>
                        @endif
                    </div>

                    {{-- Unread dot --}}
                    @unless($n->read)
                    <div class="w-2 h-2 rounded-full bg-mental-400 shrink-0 mt-2"></div>
                    @endunless
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl border border-warm-200 p-8 text-center">
                <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <p class="text-warm-500 text-sm">No notifications yet. Complete a screening to get started!</p>
            </div>
        @endif
    </div>
</div>

<script>
function markRead(id) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).catch(() => {});
}

function markAllRead() {
    const btn = document.getElementById('mark-all-btn');
    if (btn) btn.textContent = 'Marking...';

    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(function() {
        location.reload();
    }).catch(() => {
        if (btn) btn.textContent = 'Mark all read';
    });
}
</script>
@endsection

@section('footer')
@include('partials.footer')
@endsection
