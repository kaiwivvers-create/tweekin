@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Roles & Permissions</h1>
        <p class="text-sm text-warm-500">Manage user roles and their access levels.</p>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/30 text-success rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Existing Roles --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-warm-100 flex items-center justify-between">
            <h2 class="font-semibold text-warm-800">Existing Roles</h2>
        </div>
        <div class="divide-y divide-warm-100">
            @foreach($roles as $role)
            <div class="px-6 py-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <span class="font-semibold text-warm-800">{{ $role->label }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-500">{{ $role->name }}</span>
                        <span class="text-xs text-warm-400">Level {{ $role->level }}</span>
                        <span class="text-xs text-warm-400">{{ $role->users_count }} users</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @if($role->permissions && in_array('*', $role->permissions))
                        <span class="text-xs px-2 py-0.5 rounded-full bg-mental-100 text-mental-600 font-medium">All permissions</span>
                    @elseif($role->permissions)
                        @foreach($role->permissions as $perm)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-warm-100 text-warm-600">{{ $allPermissions[$perm] ?? $perm }}</span>
                        @endforeach
                    @else
                        <span class="text-xs text-warm-400">No permissions</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Create New Role --}}
    <div class="bg-white rounded-2xl border border-warm-200 p-6">
        <h2 class="font-semibold text-warm-800 mb-4">Create new role</h2>
        <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Name (slug)</label>
                    <input type="text" name="name" placeholder="e.g. moderator" required class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Display Label</label>
                    <input type="text" name="label" placeholder="e.g. Moderator" required class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-1.5">Level (0-100)</label>
                    <input type="number" name="level" value="10" min="0" max="100" required class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-warm-700 mb-2">Permissions</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($allPermissions as $key => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="rounded border-warm-300 text-mental-500 focus:ring-mental-400">
                        <span class="text-sm text-warm-600">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-mental-400 hover:bg-mental-500 border border-transparent rounded-xl font-semibold text-sm text-white transition-colors duration-200">
                Create role
            </button>
        </form>
    </div>
</div>
@endsection
