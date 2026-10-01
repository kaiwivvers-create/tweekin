@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Recent activity</h1>
            <p class="text-sm text-warm-500">Everything admins have created, edited, imported or deleted.</p>
        </div>
        @if(Auth::user()->isSuperAdmin())
            <form method="POST" action="{{ route('admin.activity.clear') }}"
                  onsubmit="return confirm('Clear the entire activity log? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-danger/40 text-danger text-sm font-semibold hover:bg-danger/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear log
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['today']) }}</div>
            <div class="text-sm text-warm-500">Changes today</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['week']) }}</div>
            <div class="text-sm text-warm-500">This week</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['total']) }}</div>
            <div class="text-sm text-warm-500">All-time entries</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['actors']) }}</div>
            <div class="text-sm text-warm-500">Admins who made changes</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-warm-200 p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-warm-500 uppercase tracking-wider mb-1.5">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Description, record or admin…"
                   class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
        </div>
        <div>
            <label class="block text-xs font-semibold text-warm-500 uppercase tracking-wider mb-1.5">Action</label>
            <select name="action" class="px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-700 text-sm focus:border-mental-400 focus:ring-0">
                <option value="">All actions</option>
                @foreach(\App\Models\ActivityLog::ACTIONS as $key => $label)
                    <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-warm-500 uppercase tracking-wider mb-1.5">Admin</label>
            <select name="actor" class="px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-700 text-sm focus:border-mental-400 focus:ring-0">
                <option value="">Anyone</option>
                @foreach($actors as $actor)
                    <option value="{{ $actor->id }}" @selected((string) request('actor') === (string) $actor->id)>{{ $actor->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-warm-500 uppercase tracking-wider mb-1.5">Record type</label>
            <select name="subject" class="px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-700 text-sm focus:border-mental-400 focus:ring-0">
                <option value="">Anything</option>
                @foreach($subjects as $type => $label)
                    <option value="{{ $type }}" @selected(request('subject') === $type)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-semibold transition-colors">Filter</button>
        @if(request()->hasAny(['q', 'action', 'actor', 'subject']))
            <a href="{{ route('admin.activity') }}" class="px-4 py-2.5 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">Reset</a>
        @endif
    </form>

    {{-- Feed --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        @forelse($logs as $log)
            @php
                $color = $log->action_color;
                $badge = match($color) {
                    'physical' => 'bg-physical-100 text-physical-700',
                    'mental' => 'bg-mental-100 text-mental-600',
                    'other' => 'bg-other-100 text-other-600',
                    'danger' => 'bg-danger/20 text-warm-700',
                    default => 'bg-warm-100 text-warm-600',
                };
                $dot = match($color) {
                    'physical' => 'bg-physical-400',
                    'mental' => 'bg-mental-400',
                    'other' => 'bg-other-400',
                    'danger' => 'bg-danger',
                    default => 'bg-warm-300',
                };
            @endphp
            <div class="px-5 sm:px-6 py-4 border-b border-warm-100 last:border-0 hover:bg-warm-50/50 transition-colors">
                <div class="flex gap-4">
                    <div class="flex flex-col items-center pt-1.5 shrink-0">
                        <span class="w-2.5 h-2.5 rounded-full {{ $dot }}"></span>
                        <span class="w-px flex-1 bg-warm-200 mt-1"></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge }}">{{ $log->action_label }}</span>
                            @if($log->subject_name)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-500 font-mono">{{ $log->subject_name }}</span>
                            @endif
                            @if($log->ip_address)
                                <span class="text-[11px] text-warm-400 font-mono">{{ $log->ip_address }}</span>
                            @endif
                        </div>

                        <p class="text-sm text-warm-800">{{ $log->description }}</p>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-warm-400">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="font-medium text-warm-500">{{ $log->user_name ?? 'System' }}</span>
                                @if($log->user_role)
                                    <span class="text-warm-400">· {{ $log->user_role }}</span>
                                @endif
                            </span>
                            <span title="{{ $log->created_at->format('M j, Y g:i A') }}">{{ $log->created_at->diffForHumans() }}</span>
                            @if($log->subject_label)
                                <span class="truncate max-w-[280px]">on “{{ $log->subject_label }}”</span>
                            @endif
                        </div>

                        @if($log->changes)
                            <details class="mt-2 group">
                                <summary class="text-xs font-medium text-mental-600 hover:text-mental-700 cursor-pointer list-none inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    {{ count($log->changes) }} field{{ count($log->changes) === 1 ? '' : 's' }} changed
                                </summary>
                                <div class="mt-2 rounded-xl border border-warm-200 overflow-hidden">
                                    <table class="w-full text-xs">
                                        <thead class="bg-warm-50">
                                            <tr>
                                                <th class="text-left px-3 py-2 font-semibold text-warm-600 w-40">Field</th>
                                                <th class="text-left px-3 py-2 font-semibold text-warm-600">Before</th>
                                                <th class="text-left px-3 py-2 font-semibold text-warm-600">After</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-warm-100">
                                            @foreach($log->changes as $field => $change)
                                                <tr>
                                                    <td class="px-3 py-2 font-mono text-warm-500 align-top">{{ $field }}</td>
                                                    <td class="px-3 py-2 text-warm-500 align-top break-all">{{ \Illuminate\Support\Str::limit($change['before'] ?? '—', 160) ?: '—' }}</td>
                                                    <td class="px-3 py-2 text-warm-800 align-top break-all">{{ \Illuminate\Support\Str::limit($change['after'] ?? '—', 160) ?: '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-16 text-center">
                <svg class="w-10 h-10 text-warm-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-warm-500 text-sm">No activity yet{{ request()->hasAny(['q', 'action', 'actor', 'subject']) ? ' matching those filters' : '' }}.</p>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
        <div>{{ $logs->links() }}</div>
    @endif
</div>
@endsection
