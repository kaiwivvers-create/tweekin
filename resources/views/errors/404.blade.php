<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page not found — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-12px) rotate(3deg); } }
        @keyframes float-delay { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-8px) rotate(-2deg); } }
        @keyframes pulse-ring { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.6); opacity: 0; } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .float-2 { animation: float-delay 5s ease-in-out infinite 0.5s; }
        .float-3 { animation: float 6s ease-in-out infinite 1s; }
        .pulse-ring { animation: pulse-ring 2s ease-out infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    {{-- Floating background shapes --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-[10%] w-40 h-40 bg-physical-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-20 right-[15%] w-48 h-48 bg-mental-100 rounded-full blur-3xl opacity-50 float-2"></div>
        <div class="absolute top-[60%] left-[60%] w-32 h-32 bg-other-100 rounded-full blur-3xl opacity-40 float-3"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        {{-- Illustration --}}
        <div class="relative mb-8">
            <svg class="w-48 h-48 mx-auto" viewBox="0 0 200 200" fill="none">
                {{-- Compass body --}}
                <circle cx="100" cy="100" r="70" fill="#FFF9E8" stroke="#F5DFA0" stroke-width="2"/>
                <circle cx="100" cy="100" r="60" fill="white" stroke="#F0E4C8" stroke-width="1"/>

                {{-- Compass markings --}}
                <line x1="100" y1="38" x2="100" y2="48" stroke="#D4C9A0" stroke-width="2" stroke-linecap="round"/>
                <line x1="100" y1="152" x2="100" y2="162" stroke="#D4C9A0" stroke-width="2" stroke-linecap="round"/>
                <line x1="38" y1="100" x2="48" y2="100" stroke="#D4C9A0" stroke-width="2" stroke-linecap="round"/>
                <line x1="152" y1="100" x2="162" y2="100" stroke="#D4C9A0" stroke-width="2" stroke-linecap="round"/>

                {{-- Needle spinning (broken) --}}
                <g class="float-1" transform-origin="100 100">
                    <polygon points="100,45 106,100 100,108 94,100" fill="#E88B8B" opacity="0.9"/>
                    <polygon points="100,155 94,100 100,92 106,100" fill="#B8C9E0" opacity="0.7"/>
                </g>

                {{-- Center dot --}}
                <circle cx="100" cy="100" r="4" fill="#D4C9A0"/>

                {{-- Question marks floating around --}}
                <text x="55" y="60" font-size="16" fill="#C9B8D9" class="float-2" font-family="sans-serif">?</text>
                <text x="140" y="65" font-size="12" fill="#B8D4C9" class="float-3" font-family="sans-serif">?</text>
                <text x="45" y="145" font-size="14" fill="#D4C9A0" class="float-1" font-family="sans-serif">?</text>
                <text x="150" y="140" font-size="10" fill="#D4B8B8" class="float-2" font-family="sans-serif">?</text>
            </svg>
        </div>

        {{-- Text --}}
        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">404</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Lost in the woods?</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">This page doesn't exist — or maybe it moved. Either way, you're not going to find it here. Let's get you back on track.</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-xl bg-physical-300 hover:bg-physical-400 text-white font-semibold transition-colors">
                Go home
            </a>
            <button onclick="history.back()" class="py-3 px-8 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold hover:bg-warm-100 transition-colors">
                Go back
            </button>
        </div>
    </div>

</body>
</html>
