<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Something broke — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-12px) rotate(3deg); } }
        @keyframes float-delay { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-8px) rotate(-2deg); } }
        @keyframes glitch { 0%, 90%, 100% { transform: translate(0); } 92% { transform: translate(-2px, 1px); } 94% { transform: translate(2px, -1px); } 96% { transform: translate(-1px, -2px); } 98% { transform: translate(1px, 2px); } }
        @keyframes flicker { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .float-2 { animation: float-delay 5s ease-in-out infinite 0.5s; }
        .glitch { animation: glitch 3s ease-in-out infinite; }
        .flicker { animation: flicker 2s ease-in-out infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    {{-- Floating background shapes --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-24 left-[15%] w-44 h-44 bg-danger/10 rounded-full blur-3xl opacity-40 float-1"></div>
        <div class="absolute bottom-16 right-[10%] w-36 h-36 bg-warning/10 rounded-full blur-3xl opacity-40 float-2"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        {{-- Illustration: Broken gear --}}
        <div class="relative mb-8">
            <svg class="w-44 h-44 mx-auto" viewBox="0 0 180 180" fill="none">
                {{-- Large gear --}}
                <g class="float-1" transform-origin="90 90">
                    <path d="M90 25 L97 35 L107 30 L105 42 L116 42 L110 52 L120 57 L111 64 L118 73 L107 73 L110 84 L100 80 L98 92 L90 84 L82 92 L80 80 L70 84 L73 73 L62 73 L69 64 L60 57 L70 52 L64 42 L75 42 L73 30 L83 35 Z" fill="#E88B8B" opacity="0.15" stroke="#E88B8B" stroke-width="2"/>
                    <circle cx="90" cy="60" r="18" fill="white" stroke="#E88B8B" stroke-width="2"/>
                    <circle cx="90" cy="60" r="6" fill="#E88B8B" opacity="0.4"/>
                </g>

                {{-- Small gear (broken, offset) --}}
                <g class="float-2 glitch" transform-origin="120 120">
                    <path d="M120 105 L125 112 L132 109 L131 117 L138 118 L134 124 L140 128 L133 132 L137 138 L130 137 L131 145 L125 142 L122 150 L118 142 L112 145 L113 137 L106 138 L110 132 L103 128 L109 124 L105 118 L112 117 L111 109 L118 112 Z" fill="#D4C9A0" opacity="0.2" stroke="#D4C9A0" stroke-width="1.5"/>
                    <circle cx="120" cy="127" r="10" fill="white" stroke="#D4C9A0" stroke-width="1.5"/>
                </g>

                {{-- Sparks / error marks --}}
                <g class="flicker">
                    <line x1="145" y1="40" x2="155" y2="35" stroke="#E88B8B" stroke-width="2" stroke-linecap="round"/>
                    <line x1="150" y1="45" x2="152" y2="55" stroke="#E88B8B" stroke-width="2" stroke-linecap="round"/>
                    <line x1="30" y1="110" x2="38" y2="105" stroke="#D4C9A0" stroke-width="2" stroke-linecap="round"/>
                    <line x1="35" y1="118" x2="33" y2="126" stroke="#D4C9A0" stroke-width="2" stroke-linecap="round"/>
                </g>

                {{-- Smoke wisps --}}
                <path d="M85 30 Q82 20 88 12" stroke="#D4C9A0" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.5"/>
                <path d="M95 28 Q98 18 93 10" stroke="#D4C9A0" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.4"/>
            </svg>
        </div>

        {{-- Text --}}
        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">500</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Something broke</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">Our gears jammed. This one's on us — not you. Try refreshing, and if it keeps happening, we'll get it sorted.</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            <a href="{{ url()->current() }}" class="py-3 px-8 rounded-xl bg-warning hover:bg-yellow-400 text-warm-800 font-semibold transition-colors">
                Try again
            </a>
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold hover:bg-warm-100 transition-colors">
                Go home
            </a>
        </div>
    </div>

</body>
</html>
