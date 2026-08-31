@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Users</h1>
        <p class="text-sm text-warm-500">Manage user accounts and roles.</p>
    </div>

    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-warm-200 bg-warm-50">
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">User</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Email</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Role</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Screenings</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Joined</th>
                        <th class="text-left px-6 py-3 font-semibold text-warm-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-mental-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <span class="font-medium text-warm-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-warm-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline">
                                @csrf
                                <select name="role_id" onchange="this.form.submit()" class="text-xs px-2 py-1 rounded-lg border border-warm-200 bg-white text-warm-600 focus:border-mental-400 focus:ring-0"
                                    {{ (!$isSuperAdmin && $user->role && $user->role->level >= 100) ? 'disabled' : '' }}>
                                    <option value="">No role</option>
                                    @foreach(\App\Models\Role::all() as $role)
                                        @if(!$isSuperAdmin && $role->level >= 100)@continue @endif
                                        <option value="{{ $role->id }}" {{ $user->role_id === $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-warm-500">{{ $user->screenings_count ?? $user->screenings()->count() }}</td>
                        <td class="px-6 py-4 text-warm-400 text-xs">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ url('/admin/screenings?user=' . $user->id) }}" class="text-xs text-mental-600 hover:text-mental-700">View screenings</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-warm-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
