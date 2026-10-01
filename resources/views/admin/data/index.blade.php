@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Data editor</h1>
            <p class="text-sm text-warm-500">Open any table and edit individual records, or run SQL directly. Super admins only.</p>
        </div>
        <a href="{{ route('admin.database') }}" class="inline-flex items-center gap-2 text-sm font-medium text-mental-600 hover:text-mental-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            Database tools
        </a>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger/20 border border-danger/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Tables --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($tables as $table => $meta)
            <a href="{{ route('admin.data.browse', $table) }}"
               class="bg-white rounded-2xl border border-warm-200 p-5 hover:border-mental-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-warm-100 group-hover:bg-mental-100 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-warm-500 group-hover:text-mental-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-600">{{ number_format($meta['rows']) }} rows</span>
                </div>
                <div class="font-semibold text-warm-800">{{ $meta['label'] }}</div>
                <div class="text-xs text-warm-400 font-mono">{{ $table }}</div>
            </a>
        @endforeach
    </div>

    {{-- SQL console --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-warm-100">
            <h2 class="font-semibold text-warm-800">SQL console</h2>
            <p class="text-xs text-warm-400 mt-0.5">SELECT statements run straight away. Anything that writes needs you to type RUN first, and is recorded in the activity log.</p>
        </div>

        <form method="POST" action="{{ route('admin.data.query') }}" class="p-6 space-y-4">
            @csrf
            <textarea name="sql" rows="5" spellcheck="false" placeholder="SELECT * FROM users ORDER BY id DESC LIMIT 20;"
                      class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-warm-50 text-warm-800 font-mono text-sm focus:border-mental-400 focus:ring-0 transition-colors resize-y">{{ old('sql', $querySql) }}</textarea>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-mental-400 hover:bg-mental-500 rounded-xl font-semibold text-sm text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Run query
                </button>
                <input type="text" name="confirm" autocomplete="off" placeholder="Type RUN for writes"
                       class="px-4 py-2.5 rounded-xl border-2 border-danger/30 bg-white text-warm-800 font-mono text-sm focus:border-danger focus:ring-0 transition-colors w-52">
                <span class="text-xs text-warm-400">Only needed for INSERT / UPDATE / DELETE / ALTER.</span>
            </div>
        </form>

        @if($queryError)
            <div class="mx-6 mb-6 bg-danger/15 border border-danger/40 rounded-xl p-4 text-sm text-warm-700 font-mono break-all">{{ $queryError }}</div>
        @endif

        @if($queryResult)
            <div class="border-t border-warm-100">
                @if(($queryResult['kind'] ?? null) === 'rows')
                    <div class="px-6 py-3 bg-warm-50 flex items-center justify-between">
                        <span class="text-xs font-semibold text-warm-600">{{ number_format($queryResult['count']) }} row(s)</span>
                        <span class="text-xs text-warm-400">Read-only query</span>
                    </div>
                    <div class="overflow-x-auto max-h-[480px] overflow-y-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-white sticky top-0 border-b border-warm-200">
                                <tr>
                                    @foreach($queryResult['columns'] as $column)
                                        <th class="text-left px-4 py-2.5 font-semibold text-warm-600 font-mono whitespace-nowrap">{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-warm-100">
                                @forelse($queryResult['rows'] as $row)
                                    <tr class="hover:bg-warm-50">
                                        @foreach($queryResult['columns'] as $column)
                                            <td class="px-4 py-2 text-warm-600 font-mono align-top max-w-[320px] break-all">
                                                {{ \Illuminate\Support\Str::limit(is_scalar($row[$column] ?? null) || ($row[$column] ?? null) === null ? ($row[$column] ?? '—') : json_encode($row[$column]), 200) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td class="px-4 py-6 text-center text-warm-400" colspan="{{ max(count($queryResult['columns']), 1) }}">No rows returned.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-4 bg-success/10 text-sm text-warm-700">
                        Statement executed. {{ $queryResult['affected'] ?? 0 }} row(s) affected.
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Recent edits --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-warm-100 flex items-center justify-between">
            <h2 class="font-semibold text-warm-800">Latest changes</h2>
            <a href="{{ route('admin.activity') }}" class="text-xs font-medium text-mental-600 hover:text-mental-700">Full activity log</a>
        </div>
        <div class="divide-y divide-warm-100">
            @forelse($recent as $entry)
                <div class="px-6 py-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-warm-100 text-warm-600">{{ $entry->action_label }}</span>
                    <span class="text-warm-700 flex-1 min-w-[200px]">{{ $entry->description }}</span>
                    <span class="text-xs text-warm-400">{{ $entry->user_name ?? 'System' }} · {{ $entry->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="px-6 py-8 text-center text-sm text-warm-400">No edits recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
