<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin — {{ config('app.name', 'Tweek') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-warm-50 text-warm-800 font-sans">

    {{-- Admin Sidebar --}}
    @php $currentRoute = request()->route()->getName(); @endphp
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:border-r lg:border-warm-800 lg:bg-warm-900 z-30">
        <div class="flex flex-col flex-1 pt-6 pb-4 overflow-y-auto">
            {{-- Brand --}}
            <div class="px-6 mb-6">
                <a href="{{ url('/admin') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center shadow-sm">
                        <span class="text-warm-800 font-bold text-sm">T</span>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Admin Panel</div>
                        <div class="text-[10px] text-warm-400">{{ config('app.name', 'Tweek') }}</div>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 space-y-1">
                <a href="{{ url('/admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ $currentRoute === 'admin.dashboard' ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Dashboard
                </a>

                <div class="pt-4 pb-1 px-3">
                    <span class="text-[10px] font-semibold text-warm-500 uppercase tracking-wider">Management</span>
                </div>

                <a href="{{ url('/admin/users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Users
                </a>

                <a href="{{ url('/admin/screenings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ str_starts_with($currentRoute, 'admin.screenings') ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Screenings
                </a>

                <a href="{{ url('/admin/reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ str_starts_with($currentRoute, 'admin.reports') ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Reports
                </a>

                <div class="pt-4 pb-1 px-3">
                    <span class="text-[10px] font-semibold text-warm-500 uppercase tracking-wider">Configuration</span>
                </div>

                <a href="{{ url('/admin/settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ str_starts_with($currentRoute, 'admin.settings') ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    App Settings
                </a>

                <a href="{{ url('/admin/brand') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ str_starts_with($currentRoute, 'admin.brand') ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    Brand Settings
                </a>

                <a href="{{ url('/admin/permissions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                    {{ str_starts_with($currentRoute, 'admin.permissions') ? 'bg-warm-700 text-white' : 'text-warm-400 hover:text-white hover:bg-warm-800' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Roles & Permissions
                </a>
            </nav>

            {{-- Bottom --}}
            <div class="px-3 mt-auto space-y-1">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-warm-400 hover:text-white hover:bg-warm-800 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to app
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-warm-400 hover:text-white hover:bg-warm-800 transition-colors w-full">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 lg:pl-64 min-h-screen">
        {{-- Top bar --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-warm-200/60 h-14 flex items-center px-6 sm:px-8 justify-between">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-success animate-pulse-soft"></div>
                <span class="text-sm font-medium text-warm-600">{{ $pageTitle ?? 'Admin' }}</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-warm-400 hidden sm:block">{{ Auth::user()->role->label ?? 'Admin' }}</span>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </header>

        <main class="p-6 sm:p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>
