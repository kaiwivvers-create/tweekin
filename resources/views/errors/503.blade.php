<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes pulse { 0%, 100% { opacity: 0.4; } 50% { opacity: 0.8; } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .pulse { animation: pulse 2s ease-in-out infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-[10%] w-40 h-40 bg-mental-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-16 right-[15%] w-36 h-36 bg-other-100 rounded-full blur-3xl opacity-50 float-1" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        <div class="relative mb-8">
            <svg class="w-44 h-44 mx-auto" viewBox="0 0 180 180" fill="none">
                <g class="float-1">
                    {{-- Wrench --}}
                    <g transform="rotate(-30 90 90)">
                        <rect x="86" y="30" width="8" height="80" rx="4" fill="#D4C9A0" stroke="#C4B890" stroke-width="1.5"/>
                        <circle cx="90" cy="30" r="14" fill="white" stroke="#D4C9A0" stroke-width="2"/>
                        <rect x="82" y="20" width="16" height="12" rx="2" fill="white" stroke="#D4C9A0" stroke-width="2"/>
                        <rect x="86" y="18" width="8" height="6" rx="1" fill="#FFF9E8"/>
                    </g>

                    {{-- Bolt --}}
                    <g transform="rotate(15 90 90)">
                        <rect x="86" y="50" width="8" height="80" rx="4" fill="#C9B8D9" stroke="#B0A0C4" stroke-width="1.5"/>
                        <polygon points="90,35 100,50 80,50" fill="#C9B8D9" stroke="#B0A0C4" stroke-width="1.5"/>
                        <rect x="82" y="120" width="16" height="8" rx="2" fill="#C9B8D9" stroke="#B0A0C4" stroke-width="1.5"/>
                    </g>
                </g>

                {{-- Pulsing circle --}}
                <circle cx="90" cy="90" r="60" fill="none" stroke="#D4C9A0" stroke-width="1" class="pulse" opacity="0.3"/>
                <circle cx="90" cy="90" r="75" fill="none" stroke="#D4C9A0" stroke-width="1" class="pulse" opacity="0.15" style="animation-delay: 0.5s;"/>
            </svg>
        </div>

        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">503</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">We're doing maintenance</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">We're making things better behind the scenes. Shouldn't take long — check back in a few minutes.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center fade-in" style="animation-delay: 0.4s;">
            <a href="{{ url()->current() }}" class="py-3 px-8 rounded-xl bg-mental-300 hover:bg-mental-400 text-white font-semibold transition-colors">
                Refresh
            </a>
            <a href="{{ route('home') }}" class="py-3 px-8 rounded-xl border-2 border-warm-200 text-warm-600 font-semibold hover:bg-warm-100 transition-colors">
                Go home
            </a>
        </div>
    </div>
</body>
</html>
