@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Database</h1>
            <p class="text-sm text-warm-500">Export a backup, restore one, or reset parts of the database. Super admins only.</p>
        </div>
        <a href="{{ route('admin.data') }}" class="inline-flex items-center gap-2 text-sm font-medium text-mental-600 hover:text-mental-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Open the data editor
        </a>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger/20 border border-danger/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['users']) }}</div>
            <div class="text-sm text-warm-500">Users</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['screenings']) }}</div>
            <div class="text-sm text-warm-500">Screenings</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-2xl font-bold text-warm-800">{{ number_format($stats['super_admins']) }}</div>
            <div class="text-sm text-warm-500">Super admins</div>
        </div>
        <div class="bg-white rounded-2xl border border-warm-200 p-5">
            <div class="text-sm font-semibold text-warm-800 font-mono truncate" title="{{ $stats['database'] }}">{{ $stats['database'] }}</div>
            <div class="text-sm text-warm-500">{{ ucfirst($stats['driver']) }} connection</div>
        </div>
    </div>

    {{-- Tables included --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-warm-100">
            <h2 class="font-semibold text-warm-800">Tables covered</h2>
            <p class="text-xs text-warm-400 mt-0.5">Only these tables are ever exported, imported or reset.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-warm-100">
            @foreach($tables as $table => $meta)
                <div class="bg-white px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-warm-800">{{ $meta['label'] }}</div>
                        <div class="text-xs text-warm-400 font-mono truncate">{{ $table }}</div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-600 shrink-0">{{ number_format($meta['rows']) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Export --}}
        <div class="bg-white rounded-2xl border border-warm-200 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-mental-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <h2 class="font-semibold text-warm-800">Export backup</h2>
            </div>
            <p class="text-sm text-warm-500 mb-4 flex-1">Downloads a single JSON file containing every table listed above, row for row. Keep it somewhere safe.</p>

            <a href="{{ route('admin.database.export') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-mental-400 hover:bg-mental-500 rounded-xl font-semibold text-sm text-white transition-colors w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download JSON backup
            </a>

            <p class="text-xs text-warm-400 mt-3">
                @if($lastExport)
                    Last export {{ $lastExport->created_at->diffForHumans() }} by {{ $lastExport->user_name }}.
                @else
                    No exports recorded yet.
                @endif
            </p>
        </div>

        {{-- Import --}}
        <div class="bg-white rounded-2xl border border-warm-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-other-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-other-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V4m0 0L8 8m4-4l4 4"/></svg>
                </div>
                <h2 class="font-semibold text-warm-800">Import backup</h2>
            </div>
            <p class="text-sm text-warm-500 mb-4">Restore a backup JSON file. Your own account is never overwritten, so you always keep access.</p>

            <form method="POST" action="{{ route('admin.database.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Backup file (.json)</label>
                    <input type="file" name="backup" accept="application/json,.json" required
                           class="w-full text-sm text-warm-600 file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-warm-100 file:text-warm-700 file:text-sm file:font-medium hover:file:bg-warm-200 cursor-pointer">
                </div>

                <div class="space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="radio" name="mode" value="merge" checked class="mt-1 border-warm-300 text-mental-500 focus:ring-mental-400">
                        <span class="text-sm text-warm-600">
                            <span class="font-semibold text-warm-800">Merge</span> — add rows and overwrite only the records that exist in the backup.
                        </span>
                    </label>
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="radio" name="mode" value="replace" class="mt-1 border-warm-300 text-danger focus:ring-danger">
                        <span class="text-sm text-warm-600">
                            <span class="font-semibold text-warm-800">Replace</span> — empty each table first, then restore. Deletes anything not in the backup.
                        </span>
                    </label>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-other-400 hover:bg-other-500 rounded-xl font-semibold text-sm text-white transition-colors">
                    Import backup
                </button>
            </form>

            <p class="text-xs text-warm-400 mt-3">
                @if($lastImport)
                    Last import {{ $lastImport->created_at->diffForHumans() }} by {{ $lastImport->user_name }}.
                @else
                    No imports recorded yet.
                @endif
            </p>
        </div>
    </div>

    {{-- Reset --}}
    <div class="bg-white rounded-2xl border-2 border-danger/40 overflow-hidden">
        <div class="px-6 py-4 border-b border-danger/20 bg-danger/5 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-danger/20 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-warm-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.5 0L3.16 16.25A2 2 0 005 19z"/></svg>
            </div>
            <div>
                <h2 class="font-semibold text-warm-800">Reset data</h2>
                <p class="text-xs text-warm-500">Permanently deletes the data you select. There is no undo — export first.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.database.reset') }}" class="p-6 space-y-5"
              onsubmit="return confirm('Reset the selected data? This cannot be undone.');">
            @csrf

            <div>
                <div class="text-[11px] font-semibold text-warm-400 uppercase tracking-wider mb-2">What should be reset?</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($resetScopes as $key => $label)
                        <label class="flex items-start gap-2.5 p-3 rounded-xl border-2 border-warm-200 hover:border-mental-200 cursor-pointer transition-colors {{ $key === 'factory' ? 'sm:col-span-2 bg-danger/5 border-danger/30' : '' }}">
                            <input type="checkbox" name="scopes[]" value="{{ $key }}"
                                   class="mt-0.5 rounded border-warm-300 {{ $key === 'factory' ? 'text-danger focus:ring-danger' : 'text-mental-500 focus:ring-mental-400' }}">
                            <span class="text-sm text-warm-600">
                                <span class="font-semibold text-warm-800">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <span class="block text-xs text-warm-500 mt-0.5">{{ $label }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl bg-warm-50 border border-warm-200 p-4 text-xs text-warm-500 space-y-1">
                <p>· Your own account and the roles table are always kept, so you cannot lock yourself out.</p>
                <p>· {{ number_format($stats['super_admins']) }} super admin account(s) currently exist — “Delete every user account” keeps only you.</p>
                <p>· “Factory reset” additionally puts roles back to Super Admin / Admin / User.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-warm-700 mb-1.5">
                    Type <span class="font-mono text-danger">RESET</span> to confirm
                </label>
                <input type="text" name="confirm" required autocomplete="off" placeholder="RESET"
                       class="w-full sm:w-56 px-4 py-2.5 rounded-xl border-2 border-danger/40 bg-white text-warm-800 font-mono text-sm focus:border-danger focus:ring-0 transition-colors">
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-danger hover:opacity-90 rounded-xl font-semibold text-sm text-warm-800 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Reset selected data
                </button>
                <p class="text-xs text-warm-400">
                    @if($lastReset)
                        Last reset {{ $lastReset->created_at->diffForHumans() }} by {{ $lastReset->user_name }}.
                    @else
                        No resets recorded yet.
                    @endif
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
