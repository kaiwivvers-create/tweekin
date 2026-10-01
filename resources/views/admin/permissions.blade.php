@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Roles &amp; Permissions</h1>
            <p class="text-sm text-warm-500">
                Edit any role, change its level, and decide exactly which pages it can reach.
                @unless($isSuperAdmin)
                    <span class="text-warm-400">Super admin roles are hidden from you.</span>
                @endunless
            </p>
        </div>
        <a href="{{ route('admin.activity') }}" class="inline-flex items-center gap-2 text-sm font-medium text-mental-600 hover:text-mental-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            View activity log
        </a>
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

    {{-- Existing roles --}}
    <div class="space-y-3">
        @foreach($roles as $role)
            @php
                $locked = !$isSuperAdmin && $role->level >= $superAdminLevel;
                $allChecked = $role->hasAllPermissions();
            @endphp
            <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
                <details class="group">
                    <summary class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 cursor-pointer list-none hover:bg-warm-50/60 transition-colors">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-semibold text-warm-800">{{ $role->label }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-500 font-mono">{{ $role->name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($role->level >= $superAdminLevel) bg-mental-100 text-mental-600
                                @elseif($role->level >= 50) bg-physical-100 text-physical-700
                                @else bg-warm-100 text-warm-500
                                @endif">Level {{ $role->level }}</span>
                            <span class="text-xs text-warm-400">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-warm-400">
                                {{ $allChecked ? 'All permissions' : (is_array($role->permissions) ? count($role->permissions) : 0).' permissions' }}
                            </span>
                            <svg class="w-4 h-4 text-warm-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </summary>

                    <div class="border-t border-warm-100 bg-warm-50/40 px-6 py-5">
                        <form method="POST" action="{{ route('admin.permissions.update', $role) }}" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Slug</label>
                                    <input type="text" name="name" value="{{ $role->name }}" required
                                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 font-mono text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Display label</label>
                                    <input type="text" name="label" value="{{ $role->label }}" required
                                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">
                                        Level
                                        <span class="font-normal text-warm-400">({{ $isSuperAdmin ? '0–100' : '0–'.($superAdminLevel - 1) }})</span>
                                    </label>
                                    <input type="number" name="level" value="{{ $role->level }}" min="0" max="{{ $isSuperAdmin ? 100 : $superAdminLevel - 1 }}" required
                                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                                </div>
                            </div>

                            <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                                <input type="checkbox" name="permissions[]" value="*"
                                       class="rounded border-warm-300 text-mental-500 focus:ring-mental-400"
                                       @checked($allChecked)>
                                <span class="text-sm font-semibold text-warm-700">All permissions (wildcard)</span>
                            </label>

                            <div class="space-y-4">
                                @foreach($catalog as $group => $permissions)
                                    <div>
                                        <div class="text-[11px] font-semibold text-warm-400 uppercase tracking-wider mb-2">{{ $group }}</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                            @foreach($permissions as $key => $label)
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                                           class="rounded border-warm-300 text-mental-500 focus:ring-mental-400"
                                                           @checked(is_array($role->permissions) && in_array($key, $role->permissions))>
                                                    <span class="text-sm text-warm-600">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-mental-400 hover:bg-mental-500 rounded-xl font-semibold text-sm text-white transition-colors">
                                    Save changes
                                </button>
                                <a href="{{ route('admin.users') }}" class="text-sm text-warm-500 hover:text-warm-700">Reassign users</a>
                            </div>
                        </form>

                        @if(!$locked)
                            <form method="POST" action="{{ route('admin.permissions.destroy', $role) }}" class="mt-4 pt-4 border-t border-warm-200"
                                  onsubmit="return confirm('Delete the role “{{ $role->label }}”? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 text-sm font-medium text-danger hover:opacity-80">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete this role
                                </button>
                            </form>
                        @endif
                    </div>
                </details>
            </div>
        @endforeach
    </div>

    {{-- Create a new role --}}
    <div class="bg-white rounded-2xl border border-warm-200 p-6">
        <h2 class="font-semibold text-warm-800 mb-1">Create a new role</h2>
        <p class="text-sm text-warm-500 mb-4">Roles control which pages show up in the sidebar and who can open them.</p>

        <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Slug</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. moderator" required
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 font-mono text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Display label</label>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="e.g. Moderator" required
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Level</label>
                    <input type="number" name="level" value="{{ old('level', 10) }}" min="0" max="{{ $isSuperAdmin ? 100 : $superAdminLevel - 1 }}" required
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                </div>
            </div>

            <div class="space-y-4">
                @foreach($catalog as $group => $permissions)
                    <div>
                        <div class="text-[11px] font-semibold text-warm-400 uppercase tracking-wider mb-2">{{ $group }}</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($permissions as $key => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                           class="rounded border-warm-300 text-mental-500 focus:ring-mental-400">
                                    <span class="text-sm text-warm-600">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-mental-400 hover:bg-mental-500 rounded-xl font-semibold text-sm text-white transition-colors">
                Create role
            </button>
        </form>
    </div>
</div>
@endsection
