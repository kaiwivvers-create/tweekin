<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page expired — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes tick { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .float-2 { animation: float 5s ease-in-out infinite 0.5s; }
        .tick { animation: tick 8s linear infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    {{-- Floating background shapes --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-16 right-[20%] w-36 h-36 bg-mental-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-24 left-[10%] w-44 h-44 bg-physical-100 rounded-full blur-3xl opacity-50 float-2"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        {{-- Illustration: Hourglass --}}
        <div class="relative mb-8">
            <svg class="w-40 h-40 mx-auto" viewBox="0 0 160 160" fill="none">
                {{-- Hourglass outline --}}
                <rect x="45" y="20" width="70" height="8" rx="4" fill="#D4C9A0" stroke="#C4B890" stroke-width="1"/>
                <rect x="45" y="132" width="70" height="8" rx="4" fill="#D4C9A0" stroke="#C4B890" stroke-width="1"/>

                {{-- Glass body --}}
                <path d="M50 28 L80 75 L50 132 L110 132 L80 75 L110 28 Z" fill="white" stroke="#D4C9A0" stroke-width="2" stroke-linejoin="round"/>

                {{-- Sand (top) --}}
                <path d="M52 30 L108 30 L82 68 L78 68 Z" fill="#F5DFA0" opacity="0.6"/>
                <path d="M52 30 L108 30 L100 42 L60 42 Z" fill="#F5DFA0" opacity="0.4"/>

                {{-- Sand (bottom) --}}
                <path d="M60 120 L100 120 L108 130 L52 130 Z" fill="#F5DFA0" opacity="0.7"/>

                {{-- Dripping sand --}}
                <g class="tick" transform-origin="80 80">
                    <line x1="80" y1="68" x2="80" y2="115" stroke="#F5DFA0" stroke-width="2" stroke-dasharray="3 4"/>
                </g>

                {{-- Clock hands on the side --}}
                <circle cx="25" cy="80" r="12" fill="white" stroke="#D4C9A0" stroke-width="1.5"/>
                <line x1="25" y1="80" x2="25" y2="72" stroke="#D4C9A0" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="25" y1="80" x2="31" y2="80" stroke="#D4C9A0" stroke-width="1.5" stroke-linecap="round"/>

                <circle cx="135" cy="80" r="12" fill="white" stroke="#D4C9A0" stroke-width="1.5"/>
                <line x1="135" y1="80" x2="135" y2="72" stroke="#D4C9A0" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="135" y1="80" x2="129" y2="80" stroke="#D4C9A0" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>

        {{-- Text --}}
        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">419</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Session expired</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">Your session timed out — it happens. No data was lost, you just need to refresh and try again.</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            <a href="{{ url()->current() }}" class="py-3 px-8 rounded-xl bg-mental-300 hover:bg-mental-400 text-white font-semibold transition-colors">
                Refresh page
            </a>
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold hover:bg-warm-100 transition-colors">
                Go home
            </a>
        </div>
    </div>

</body>
</html>
