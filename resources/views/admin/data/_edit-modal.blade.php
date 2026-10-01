{{--
    Inline editor modal for a single record.

    It lives in a <template>, so nothing inside is parsed or rendered until
    openModal() clones it, which keeps a 20-row list cheap.

    Expects the same variables as _form.blade.php, plus $record.
--}}
<template id="modal-edit-{{ $table }}-{{ $record->id }}" data-modal-size="xl">
    <div class="p-6">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h3 class="font-display font-bold text-lg text-warm-800">
                    Edit {{ Str::singular($meta['label']) }}
                    <span class="font-mono text-warm-400 text-sm">#{{ $record->id }}</span>
                </h3>
                <p class="text-xs text-warm-500 mt-0.5">
                    {{ $record->{$meta['title']} ?? 'Record' }}
                    <span class="text-warm-400">· every change is recorded in the activity log</span>
                </p>
            </div>
            <button type="button" onclick="closeModal()" aria-label="Close"
                    class="text-warm-400 hover:text-warm-600 transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @include('admin.data._form', [
            'table' => $table,
            'meta' => $meta,
            'record' => $record,
            'columns' => $columns,
            'jsonColumns' => $jsonColumns,
            'roles' => $roles,
            'compact' => true,
            'returnTo' => request()->fullUrl(),
        ])
    </div>
</template>
