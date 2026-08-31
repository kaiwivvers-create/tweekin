<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slow down — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes steam { 0% { transform: translateY(0) scaleX(1); opacity: 0.6; } 100% { transform: translateY(-20px) scaleX(1.5); opacity: 0; } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .steam-1 { animation: steam 2s ease-out infinite; }
        .steam-2 { animation: steam 2s ease-out infinite 0.5s; }
        .steam-3 { animation: steam 2s ease-out infinite 1s; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-24 left-[18%] w-36 h-36 bg-physical-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-20 right-[12%] w-40 h-40 bg-mental-100 rounded-full blur-3xl opacity-50 float-1" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        <div class="relative mb-8">
            <svg class="w-40 h-40 mx-auto" viewBox="0 0 160 160" fill="none">
                <g class="float-1">
                    {{-- Steam --}}
                    <path d="M65 55 Q60 45 65 35" stroke="#D4C9A0" stroke-width="2" fill="none" stroke-linecap="round" class="steam-1"/>
                    <path d="M80 52 Q85 40 80 30" stroke="#D4C9A0" stroke-width="2" fill="none" stroke-linecap="round" class="steam-2"/>
                    <path d="M95 55 Q100 45 95 35" stroke="#D4C9A0" stroke-width="2" fill="none" stroke-linecap="round" class="steam-3"/>

                    {{-- Cup --}}
                    <rect x="50" y="60" width="60" height="50" rx="8" fill="white" stroke="#D4C9A0" stroke-width="2"/>
                    <rect x="55" y="65" width="50" height="10" rx="4" fill="#F5DFA0" opacity="0.5"/>

                    {{-- Handle --}}
                    <path d="M110 72 C125 72 125 98 110 98" stroke="#D4C9A0" stroke-width="2" fill="none"/>

                    {{-- Saucer --}}
                    <ellipse cx="80" cy="115" rx="45" ry="10" fill="white" stroke="#D4C9A0" stroke-width="2"/>

                    {{-- Coffee liquid --}}
                    <ellipse cx="80" cy="72" rx="25" ry="5" fill="#D4B88C" opacity="0.4"/>
                </g>
            </svg>
        </div>

        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">429</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Slow down there</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">You're doing too much too fast. Take a breather, grab a coffee, and try again in a minute.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            <a href="{{ url()->current() }}" class="py-3 px-8 rounded-xl bg-physical-300 hover:bg-physical-400 text-white font-semibold transition-colors">
                Try again
            </a>
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold hover:bg-warm-100 transition-colors">
                Go home
            </a>
        </div>
    </div>
</body>
</html>
