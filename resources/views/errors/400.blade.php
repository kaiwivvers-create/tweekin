<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bad request — {{ config('app.name', 'Tweek') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes snap { 0%, 85%, 100% { transform: rotate(0deg); } 90% { transform: rotate(5deg); } 95% { transform: rotate(-3deg); } }
        @keyframes fade-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .float-1 { animation: float 4s ease-in-out infinite; }
        .snap { animation: snap 4s ease-in-out infinite; }
        .fade-in { animation: fade-in 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-warm-50 min-h-screen flex items-center justify-center px-4 overflow-hidden relative">

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-16 left-[12%] w-36 h-36 bg-physical-100 rounded-full blur-3xl opacity-50 float-1"></div>
        <div class="absolute bottom-20 right-[18%] w-40 h-40 bg-mental-100 rounded-full blur-3xl opacity-50 float-1" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg">
        <div class="relative mb-8">
            <svg class="w-40 h-40 mx-auto" viewBox="0 0 160 160" fill="none">
                {{-- Broken chain link --}}
                <g class="float-1">
                    <ellipse cx="55" cy="80" rx="25" ry="18" fill="white" stroke="#D4C9A0" stroke-width="2.5"/>
                    <ellipse cx="105" cy="80" rx="25" ry="18" fill="white" stroke="#D4C9A0" stroke-width="2.5"/>
                </g>
                {{-- Break mark --}}
                <g class="snap">
                    <line x1="78" y1="70" x2="85" y2="65" stroke="#E88B8B" stroke-width="2" stroke-linecap="round"/>
                    <line x1="80" y1="80" x2="88" y2="80" stroke="#E88B8B" stroke-width="2" stroke-linecap="round"/>
                    <line x1="78" y1="90" x2="85" y2="95" stroke="#E88B8B" stroke-width="2" stroke-linecap="round"/>
                </g>
                {{-- Sparks --}}
                <circle cx="82" cy="60" r="2" fill="#E88B8B" opacity="0.5"/>
                <circle cx="90" cy="75" r="1.5" fill="#E88B8B" opacity="0.4"/>
                <circle cx="86" cy="98" r="2" fill="#E88B8B" opacity="0.3"/>
            </svg>
        </div>

        <div class="fade-in" style="animation-delay: 0.2s;">
            <h1 class="font-display font-bold text-6xl sm:text-7xl text-warm-300 mb-2">400</h1>
            <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-700 mb-3">Something's not right</h2>
            <p class="text-warm-500 leading-relaxed mb-8 max-w-sm mx-auto">The request got mangled somewhere along the way. This usually means a link is broken or something was typed wrong.</p>
        </div>

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
