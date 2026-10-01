@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div>
        <nav class="flex items-center gap-2 text-xs text-warm-400 mb-1.5">
            <a href="{{ route('admin.data') }}" class="hover:text-mental-600 transition-colors">Data editor</a>
            <span>/</span>
            <a href="{{ route('admin.data.browse', $table) }}" class="hover:text-mental-600 transition-colors">{{ $meta['label'] }}</a>
            <span>/</span>
            <span class="text-warm-600 font-mono">#{{ $record->id }}</span>
        </nav>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">
            Edit {{ Str::singular($meta['label']) }}
            <span class="font-mono text-warm-400 text-lg">#{{ $record->id }}</span>
        </h1>
        <p class="text-sm text-warm-500">
            {{ $record->{$meta['title']} ?? 'Record' }}
            <span class="text-warm-400">· every change is recorded in the activity log</span>
        </p>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger/20 border border-danger/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-danger/20 border border-danger/40 text-warm-700 rounded-xl p-4 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-warm-200 p-6">
        @include('admin.data._form', [
            'table' => $table,
            'meta' => $meta,
            'record' => $record,
            'columns' => $columns,
            'jsonColumns' => $jsonColumns,
            'roles' => $roles,
            'compact' => false,
        ])
    </div>

    <div class="bg-white rounded-2xl border-2 border-danger/30 p-6">
        <h2 class="font-semibold text-warm-800 mb-1">Delete this record</h2>
        <p class="text-sm text-warm-500 mb-4">Removes the row from <span class="font-mono">{{ $table }}</span> immediately. Related rows may be affected.</p>
        <form method="POST" action="{{ route('admin.data.destroy', [$table, $record->id]) }}"
              onsubmit="return confirm('Delete this record permanently?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-danger/50 text-danger text-sm font-semibold hover:bg-danger/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete record
            </button>
        </form>
    </div>
</div>
@endsection
