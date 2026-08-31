<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tweek') }} — {{ request()->is('login') ? 'Log in' : 'Sign up' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-20px) rotate(5deg); } }
        @keyframes float-reverse { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(20px) rotate(-5deg); } }
        @keyframes drift { 0%, 100% { transform: translate(0, 0); } 25% { transform: translate(10px, -15px); } 50% { transform: translate(-5px, -25px); } 75% { transform: translate(-15px, -10px); } }
        @keyframes pulse-ring { 0% { transform: scale(1); opacity: 0.4; } 50% { transform: scale(1.05); opacity: 0.2; } 100% { transform: scale(1); opacity: 0.4; } }
        .float { animation: float 6s ease-in-out infinite; }
        .float-delay { animation: float 6s ease-in-out 1s infinite; }
        .float-reverse { animation: float-reverse 7s ease-in-out infinite; }
        .drift { animation: drift 8s ease-in-out infinite; }
        .drift-delay { animation: drift 10s ease-in-out 2s infinite; }
        .pulse-ring { animation: pulse-ring 4s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex bg-warm-50 text-warm-800 font-sans">
    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
    </script>

    @php
        $isLogin = request()->is('login');
        $appName = $brand['name'] ?? config('app.name', 'Tweek');
    @endphp

    {{-- Left side: Decorative illustration --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center
        @if($isLogin) bg-gradient-to-br from-mental-50 via-physical-50 to-other-50
        @else bg-gradient-to-br from-other-50 via-mental-50 to-physical-50
        @endif
    ">
        {{-- Background blobs --}}
        @if($isLogin)
            <div class="absolute top-20 left-20 w-72 h-72 bg-mental-200/30 rounded-full blur-3xl drift"></div>
            <div class="absolute bottom-20 right-20 w-64 h-64 bg-physical-200/30 rounded-full blur-3xl drift-delay"></div>
            <div class="absolute top-1/2 left-1/3 w-48 h-48 bg-other-200/20 rounded-full blur-3xl drift"></div>
        @else
            <div class="absolute top-20 left-20 w-72 h-72 bg-other-200/30 rounded-full blur-3xl drift"></div>
            <div class="absolute bottom-20 right-20 w-64 h-64 bg-mental-200/30 rounded-full blur-3xl drift-delay"></div>
            <div class="absolute top-1/2 right-1/4 w-48 h-48 bg-physical-200/20 rounded-full blur-3xl drift"></div>
        @endif

        {{-- Floating shapes --}}
        <div class="relative z-10 w-full max-w-md px-12">
            {{-- Main logo --}}
            <div class="float mb-8">
                <div class="w-24 h-24 rounded-3xl bg-white/80 backdrop-blur-sm border border-warm-200/50 shadow-xl flex items-center justify-center mx-auto overflow-hidden">
                    @if($brand['logo_path'] ?? '')
                        <img src="{{ Storage::disk('public')->url($brand['logo_path']) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center">
                            <span class="text-warm-800 font-bold text-2xl">T</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($isLogin)
                <h2 class="font-display font-bold text-3xl text-warm-800 text-center mb-3">Welcome back to {{ $appName }}</h2>
                <p class="text-warm-500 text-center text-sm leading-relaxed mb-10">Log in to pick up where you left off.</p>
            @else
                <h2 class="font-display font-bold text-3xl text-warm-800 text-center mb-3">Welcome to {{ $appName }}</h2>
                <p class="text-warm-500 text-center text-sm leading-relaxed mb-10">A preliminary symptom checker that helps you understand what you're experiencing.</p>
            @endif

            {{-- Floating icons — different order for login vs register --}}
            <div class="relative h-48">
                @if($isLogin)
                    {{-- Login order: Heart, Shield, Brain, Clock, Question --}}
                    <div class="absolute top-0 left-8 float">
                        <div class="w-12 h-12 rounded-2xl bg-white/80 backdrop-blur-sm border border-physical-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                    </div>
                    <div class="absolute top-4 right-12 float-delay">
                        <div class="w-12 h-12 rounded-2xl bg-white/80 backdrop-blur-sm border border-success/30 shadow-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>
                    <div class="absolute top-20 left-1/2 -translate-x-1/2 float-reverse">
                        <div class="w-14 h-14 rounded-2xl bg-white/80 backdrop-blur-sm border border-mental-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-16 float">
                        <div class="w-10 h-10 rounded-xl bg-white/80 backdrop-blur-sm border border-warm-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-8 right-16 float-delay">
                        <div class="w-10 h-10 rounded-xl bg-white/80 backdrop-blur-sm border border-other-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                @else
                    {{-- Register order: Question, Brain, Heart, Shield, Clock --}}
                    <div class="absolute top-0 left-8 float">
                        <div class="w-12 h-12 rounded-2xl bg-white/80 backdrop-blur-sm border border-other-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="absolute top-4 right-12 float-delay">
                        <div class="w-12 h-12 rounded-2xl bg-white/80 backdrop-blur-sm border border-mental-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                    </div>
                    <div class="absolute top-20 left-1/2 -translate-x-1/2 float-reverse">
                        <div class="w-14 h-14 rounded-2xl bg-white/80 backdrop-blur-sm border border-physical-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-16 float">
                        <div class="w-10 h-10 rounded-xl bg-white/80 backdrop-blur-sm border border-success/30 shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-8 right-16 float-delay">
                        <div class="w-10 h-10 rounded-xl bg-white/80 backdrop-blur-sm border border-warm-200/50 shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Decorative rings --}}
            @if($isLogin)
                <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-64 h-64 border border-mental-200/20 rounded-full pulse-ring"></div>
                <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-80 h-80 border border-physical-200/15 rounded-full pulse-ring" style="animation-delay: 1s;"></div>
            @else
                <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-64 h-64 border border-other-200/20 rounded-full pulse-ring"></div>
                <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-80 h-80 border border-mental-200/15 rounded-full pulse-ring" style="animation-delay: 1s;"></div>
            @endif
        </div>
    </div>

    {{-- Right side: Form --}}
    <div class="w-full lg:w-1/2 flex flex-col">
        {{-- Top bar --}}
        <div class="h-14 px-6 sm:px-8 flex items-center justify-between border-b border-warm-200/60">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group lg:hidden">
                @if($brand['logo_path'] ?? '')
                    <img src="{{ Storage::disk('public')->url($brand['logo_path']) }}" alt="Logo" class="w-7 h-7 rounded-lg object-cover shadow-sm border border-warm-200">
                @else
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center shadow-sm border border-warm-200">
                        <span class="text-warm-800 font-bold text-xs">T</span>
                    </div>
                @endif
                <span class="font-display font-bold text-base text-warm-800 tracking-tight">{{ $appName }}</span>
            </a>
            <div class="hidden lg:block"></div>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-sm font-medium text-warm-500 hover:text-warm-700 transition-colors">Home</a>
                @if($isLogin)
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-mental-100 hover:bg-mental-200 text-mental-700 px-4 py-1.5 rounded-full transition-colors">Sign up</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium bg-physical-100 hover:bg-physical-200 text-physical-700 px-4 py-1.5 rounded-full transition-colors">Log in</a>
                @endif
            </div>
        </div>

        {{-- Form area --}}
        <div class="flex-1 flex items-center justify-center px-6 sm:px-12 py-12 relative overflow-hidden">
            {{-- Background decorative dots --}}
            <div class="absolute inset-0 opacity-[0.03]">
                <div class="absolute top-10 right-10 w-2 h-2 rounded-full bg-warm-800"></div>
                <div class="absolute top-20 left-20 w-1.5 h-1.5 rounded-full bg-warm-800"></div>
                <div class="absolute top-32 right-32 w-1 h-1 rounded-full bg-warm-800"></div>
                <div class="absolute bottom-20 left-16 w-2 h-2 rounded-full bg-warm-800"></div>
                <div class="absolute bottom-32 right-20 w-1.5 h-1.5 rounded-full bg-warm-800"></div>
                <div class="absolute top-1/2 left-10 w-1 h-1 rounded-full bg-warm-800"></div>
                <div class="absolute top-1/3 right-14 w-2 h-2 rounded-full bg-warm-800"></div>
            </div>

            <div class="w-full max-w-sm relative z-10">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>
