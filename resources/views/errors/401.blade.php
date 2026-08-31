<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Not authenticated — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes blink { 0%, 45%, 55%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .blink { animation: blink 2s ease-in-out infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 right-[15%] w-40 h-40 bg-mental-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-16 left-[10%] w-32 h-32 bg-physical-100 rounded-full blur-3xl opacity-50 float-1" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        <div class="relative mb-8">
            <svg class="w-40 h-40 mx-auto" viewBox="0 0 160 160" fill="none">
                <g class="float-1">
                    <circle cx="80" cy="70" r="40" fill="white" stroke="#D4C9A0" stroke-width="2"/>
                    <circle cx="80" cy="70" r="30" fill="#FFF9E8" stroke="#EDE0C0" stroke-width="1"/>
                    {{-- Eyes (blink) --}}
                    <g class="blink">
                        <ellipse cx="68" cy="65" rx="3" ry="4" fill="#D4C9A0"/>
                        <ellipse cx="92" cy="65" rx="3" ry="4" fill="#D4C9A0"/>
                    </g>
                    {{-- Mouth (confused) --}}
                    <path d="M70 82 Q80 78 90 82" stroke="#D4C9A0" stroke-width="2" fill="none" stroke-linecap="round"/>
                    {{-- Question mark above head --}}
                    <text x="73" y="38" font-size="22" fill="#C9B8D9" font-weight="bold" font-family="sans-serif">?</text>
                </g>
                {{-- Body --}}
                <rect x="65" y="110" width="30" height="35" rx="8" fill="white" stroke="#D4C9A0" stroke-width="2"/>
                <circle cx="80" cy="127" r="3" fill="#D4C9A0" opacity="0.4"/>
            </svg>
        </div>

        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">401</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Who are you again?</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">You need to log in to access this page. No worries — it's quick.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            <a href="{{ route('login') }}" class="py-3 px-8 rounded-xl bg-physical-300 hover:bg-physical-400 text-white font-semibold transition-colors">
                Log in
            </a>
            <a href="{{ route('register') }}" class="py-3 px-8 rounded-xl bg-mental-300 hover:bg-mental-400 text-white font-semibold transition-colors">
                Sign up
            </a>
        </div>
    </div>
</body>
</html>
