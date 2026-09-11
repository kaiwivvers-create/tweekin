@php
    $currentRoute = request()->route()->getName();
@endphp

<aside class="app-sidebar hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:top-14 lg:border-r lg:border-warm-200 lg:bg-white/60 lg:backdrop-blur-sm z-30">
    <div class="flex flex-col flex-1 pt-6 pb-4 overflow-y-auto">
        {{-- Navigation --}}
        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ $currentRoute === 'dashboard' ? 'bg-mental-100 text-mental-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Screenings</span>
            </div>

            <a href="{{ route('physical.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'physical') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <div class="w-5 h-5 rounded-md bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                Physical
            </a>

            <a href="{{ route('mental.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'mental') ? 'bg-mental-100 text-mental-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <div class="w-5 h-5 rounded-md bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                Mental
            </a>

            <a href="{{ route('other.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'other') ? 'bg-other-100 text-other-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <div class="w-5 h-5 rounded-md bg-other-100 border border-other-200 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                Not sure?
            </a>

            <div class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Tools</span>
            </div>

            <a href="{{ route('compare.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'compare') ? 'bg-mental-100 text-mental-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Compare
            </a>

            <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'notifications') ? 'bg-mental-100 text-mental-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
            </a>

            @php
                $user = Auth::user();
                $canSeeAdmin = false;
                if ($user && $user->role && ($user->role->level >= 50 || in_array('settings.edit', $user->role->permissions ?? []))) {
                    $canSeeAdmin = true;
                }
            @endphp

            @if($canSeeAdmin)
            @php
                $adminPerms = $user->role->permissions ?? [];
                $isSuperAdmin = $user->role->level >= 100;
                $canUsers = $isSuperAdmin || in_array('users.view', $adminPerms);
                $canScreenings = $isSuperAdmin || in_array('screenings.view', $adminPerms);
                $canReports = $isSuperAdmin || in_array('reports.view', $adminPerms);
                $canSettings = $isSuperAdmin || in_array('settings.edit', $adminPerms);
                $canRoles = $isSuperAdmin || in_array('roles.manage', $adminPerms);
            @endphp
            <div class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Admin</span>
            </div>

            <a href="{{ url('/admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ $currentRoute === 'admin.dashboard' ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Dashboard
            </a>

            @if($canUsers)
            <a href="{{ url('/admin/users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Users
            </a>
            @endif

            @if($canScreenings)
            <a href="{{ url('/admin/screenings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'admin.screenings') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Screenings
            </a>
            @endif

            @if($canReports)
            <a href="{{ url('/admin/reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'admin.reports') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Reports
            </a>
            @endif

            @if($canSettings)
            <a href="{{ url('/admin/brand') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'admin.brand') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                Brand Settings
            </a>
            @endif

            @if($canRoles)
            <a href="{{ url('/admin/permissions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'admin.permissions') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Roles & Permissions
            </a>
            @endif
            @endif
        </nav>

        {{-- Bottom: Logout --}}
        <div class="px-3 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-warm-500 hover:text-warm-700 hover:bg-warm-100 transition-colors w-full">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Log out
                </button>
            </form>
        </div>
    </div>
</aside>
