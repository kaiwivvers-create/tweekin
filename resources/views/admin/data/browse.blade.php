@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
@php
    $hiddenCols = ['data', 'changes', 'permissions', 'avatar', 'remember_token'];
    $names = collect($columns)->pluck('name')->reject(fn ($n) => in_array($n, $hiddenCols, true));
    if ($titleColumn) {
        $names = collect([$titleColumn])->merge($names->reject(fn ($n) => $n === $titleColumn));
    }
    $names = collect(['id'])->merge($names->reject(fn ($n) => $n === 'id'))->unique()->take(6)->all();
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-warm-400 mb-1.5">
                <a href="{{ route('admin.data') }}" class="hover:text-mental-600 transition-colors">Data editor</a>
                <span>/</span>
                <span class="text-warm-600">{{ $meta['label'] }}</span>
            </nav>
            <h1 class="font-display font-bold text-2xl text-warm-800">{{ $meta['label'] }}</h1>
            <p class="text-sm text-warm-500 font-mono">{{ $table }} · {{ number_format($records->total()) }} rows</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search {{ strtolower($meta['label']) }}…"
                   class="px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors w-56">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-warm-100 hover:bg-warm-200 text-warm-700 text-sm font-medium transition-colors">Search</button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger/20 border border-danger/40 text-warm-700 rounded-xl p-4 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-warm-200 bg-warm-50">
                        @foreach($names as $name)
                            <th class="text-left px-5 py-3 font-semibold text-warm-700 font-mono whitespace-nowrap">
                                <a href="{{ route('admin.data.browse', array_merge(['table' => $table], request()->except('page'), ['sort' => $name, 'direction' => ($sort === $name && $direction === 'desc') ? 'asc' : 'desc'])) }}"
                                   class="inline-flex items-center gap-1 hover:text-mental-600 transition-colors">
                                    {{ $name }}
                                    @if($sort === $name)
                                        <span class="text-[10px]">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="text-right px-5 py-3 font-semibold text-warm-700 whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-100">
                    @forelse($records as $record)
                        <tr class="hover:bg-warm-50 transition-colors">
                            @foreach($names as $name)
                                @php $value = $record->{$name} ?? null; @endphp
                                <td class="px-5 py-3 text-warm-600 align-top">
                                    @if($name === 'id')
                                        <span class="font-mono text-xs text-warm-400">#{{ $value }}</span>
                                    @elseif(is_bool($value))
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $value ? 'bg-success/25 text-warm-700' : 'bg-warm-100 text-warm-500' }}">{{ $value ? 'true' : 'false' }}</span>
                                    @elseif(is_array($value) || is_object($value))
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-other-100 text-other-600 font-mono">json</span>
                                    @elseif($value === null)
                                        <span class="text-warm-300">—</span>
                                    @else
                                        <span class="{{ $name === $titleColumn ? 'font-medium text-warm-800' : '' }}">
                                            {{ \Illuminate\Support\Str::limit((string) $value, 60) }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-3">
                                    <button type="button" onclick="openModal('edit-{{ $table }}-{{ $record->id }}')"
                                            class="text-xs font-medium text-mental-600 hover:text-mental-700">Edit</button>
                                    <a href="{{ route('admin.data.show', [$table, $record->id]) }}" class="text-xs text-warm-400 hover:text-warm-600">Open page</a>
                                    <form method="POST" action="{{ route('admin.data.destroy', [$table, $record->id]) }}" class="inline"
                                          onsubmit="return confirm('Delete this record permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-danger hover:opacity-80">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($names) + 1 }}" class="px-5 py-12 text-center text-warm-400">
                                No records{{ request('q') ? ' match that search' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="px-5 py-4 border-t border-warm-100">{{ $records->links() }}</div>
        @endif
    </div>

    <p class="text-xs text-warm-400">
        Showing the most useful columns. Edit a record to see every field, or open it on its own page.
    </p>

    {{-- One inert editor modal per row, cloned into the dialog on demand. --}}
    @foreach($records as $record)
        @include('admin.data._edit-modal', [
            'table' => $table,
            'meta' => $meta,
            'columns' => $columns,
            'jsonColumns' => $jsonColumns,
            'roles' => $roles,
            'record' => $record,
        ])
    @endforeach
</div>

@if(session('error') && old('_record'))
    {{-- A save failed: reopen that same modal, with the typed values still in it. --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            openModal('edit-{{ $table }}-' + @json(old('_record')));
        });
    </script>
@endif
@endsection
