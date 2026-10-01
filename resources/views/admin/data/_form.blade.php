{{--
    Record editor form. Shared by the full page (admin/data/edit.blade.php) and
    the inline per-row modals on the admin lists.

    Expects: $table, $meta, $record, $columns, $jsonColumns, $roles
    Optional: $compact (modal layout), $returnTo (URL to land on after saving)
--}}
@php
    $readOnly = ['id', 'created_at', 'updated_at'];

    // The data browser hands us stdClass rows, the admin lists hand us Eloquent
    // models — and a model keeps its attributes in a protected property, so a
    // plain (array) cast would come back empty and blank out every field.
    $recordArray = $record instanceof \Illuminate\Database\Eloquent\Model
        ? $record->getAttributes()
        : (array) $record;

    $compact = $compact ?? false;

    // Only repopulate the record that actually failed, otherwise a validation
    // error on one row would leak its typed values into every other row's modal.
    $useOldInput = (string) old('_record') === (string) $record->id;

    $prettyJson = function ($value) {
        if ($value === null || $value === '') return '';
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value;
    };
@endphp

<form method="POST" action="{{ route('admin.data.update', [$table, $record->id]) }}" class="space-y-5">
    @csrf
    @method('PUT')
    <input type="hidden" name="_record" value="{{ $record->id }}">
    @if(!empty($returnTo))
        <input type="hidden" name="_return" value="{{ $returnTo }}">
    @endif

    <div class="grid grid-cols-1 {{ $compact ? 'sm:grid-cols-2' : 'lg:grid-cols-2' }} gap-4 {{ $compact ? '' : 'gap-5' }}">
        @foreach($columns as $column)
            @php
                $name = $column['name'];
                $type = strtolower((string) $column['type']);
                $value = $useOldInput ? old($name, $recordArray[$name] ?? null) : ($recordArray[$name] ?? null);
                $isJson = in_array($name, $jsonColumns, true);
                $isLocked = in_array($name, $readOnly, true) || $column['auto'];
                $isLongText = str_contains($type, 'text') || str_contains($type, 'clob');
                $isDate = str_contains($type, 'date') || str_contains($type, 'time');
                $isBool = str_contains($type, 'bool');
            @endphp

            <div class="{{ ($isJson || $isLongText || $name === 'avatar') ? 'sm:col-span-2' : '' }}">
                <label class="flex items-center gap-2 text-sm font-semibold text-warm-700 mb-1.5">
                    <span class="font-mono">{{ $name }}</span>
                    <span class="text-[10px] font-normal uppercase tracking-wider text-warm-400">{{ $type }}{{ $column['nullable'] ? ' · nullable' : '' }}</span>
                    @if($isLocked)
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-warm-100 text-warm-400 font-medium">read only</span>
                    @endif
                    @if($isJson)
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-other-100 text-other-600 font-medium">json</span>
                    @endif
                </label>

                @if($isLocked)
                    <input type="text" value="{{ is_scalar($value) || $value === null ? $value : json_encode($value) }}" disabled
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-100 bg-warm-50 text-warm-400 font-mono text-sm">
                @elseif($table === 'users' && $name === 'role_id')
                    <select name="role_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                        <option value="">No role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) $value === (string) $role->id)>
                                {{ $role->label }} (level {{ $role->level }})
                            </option>
                        @endforeach
                    </select>
                @elseif($table === 'users' && $name === 'password')
                    <input type="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep the current password"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                    <p class="text-xs text-warm-400 mt-1.5">Filled in? It gets hashed before saving. Changing this signs nobody out.</p>
                @elseif($isBool)
                    <select name="{{ $name }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                        <option value="1" @selected((bool) $value === true)>true</option>
                        <option value="0" @selected((bool) $value === false)>false</option>
                    </select>
                @elseif($isJson)
                    <textarea name="{{ $name }}" rows="{{ $compact ? 6 : 10 }}" spellcheck="false"
                              class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-warm-50 text-warm-800 font-mono text-xs focus:border-mental-400 focus:ring-0 transition-colors resize-y">{{ $prettyJson($value) }}</textarea>
                    <p class="text-xs text-warm-400 mt-1.5">Must stay valid JSON.</p>
                @elseif($isLongText)
                    <textarea name="{{ $name }}" rows="{{ $compact ? 4 : 5 }}"
                              class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors resize-y">{{ $value }}</textarea>
                @elseif($isDate)
                    <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="YYYY-MM-DD HH:MM:SS"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 font-mono text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                @else
                    <input type="{{ str_contains($type, 'int') || str_contains($type, 'float') || str_contains($type, 'decimal') ? 'number' : 'text' }}"
                           name="{{ $name }}" value="{{ $value }}" step="any"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                @endif
            </div>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-mental-400 hover:bg-mental-500 rounded-xl font-semibold text-sm text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save record
        </button>

        @if($compact)
            <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">
                Cancel
            </button>
        @else
            <a href="{{ route('admin.data.browse', array_merge(['table' => $table], request()->only('q', 'page'))) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">
                Back to list
            </a>
        @endif
    </div>
</form>
