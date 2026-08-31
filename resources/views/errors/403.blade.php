<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access denied — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-10px) rotate(2deg); } }
        @keyframes sway { 0%, 100% { transform: rotate(-3deg); } 50% { transform: rotate(3deg); } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .sway { animation: sway 3s ease-in-out infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 right-[20%] w-40 h-40 bg-other-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-20 left-[15%] w-32 h-32 bg-physical-100 rounded-full blur-3xl opacity-50 float-1" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        {{-- Illustration: Shield with lock --}}
        <div class="relative mb-8">
            <svg class="w-40 h-40 mx-auto" viewBox="0 0 160 160" fill="none">
                {{-- Shield --}}
                <g class="float-1">
                    <path d="M80 15 L130 35 L130 85 C130 115 108 140 80 150 C52 140 30 115 30 85 L30 35 Z" fill="#F3EEFF" stroke="#C9B8D9" stroke-width="2"/>
                    <path d="M80 25 L120 42 L120 82 C120 108 102 130 80 138 C58 130 40 108 40 82 L40 42 Z" fill="white" stroke="#DDD0EB" stroke-width="1"/>
                </g>

                {{-- Lock --}}
                <g class="sway" transform-origin="80 95">
                    <rect x="62" y="80" width="36" height="28" rx="4" fill="#C9B8D9" stroke="#B0A0C4" stroke-width="1.5"/>
                    <path d="M68 80 L68 68 C68 58 73 52 80 52 C87 52 92 58 92 68 L92 80" stroke="#B0A0C4" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <circle cx="80" cy="93" r="3" fill="white"/>
                    <line x1="80" y1="95" x2="80" y2="101" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </g>

                {{-- Small decorative elements --}}
                <circle cx="25" cy="55" r="3" fill="#C9B8D9" opacity="0.4"/>
                <circle cx="135" cy="70" r="2" fill="#C9B8D9" opacity="0.3"/>
                <circle cx="50" cy="140" r="2" fill="#C9B8D9" opacity="0.3"/>
            </svg>
        </div>

        {{-- Text --}}
        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">403</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Access denied</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">You don't have permission to view this page. It might be restricted, or you might need to log in with the right account.</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            @if(!auth()->check())
            <a href="{{ route('login') }}" class="py-3 px-8 rounded-xl bg-physical-300 hover:bg-physical-400 text-white font-semibold transition-colors">
                Log in
            </a>
            @endif
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold hover:bg-warm-100 transition-colors">
                Go home
            </a>
        </div>
    </div>

</body>
</html>
