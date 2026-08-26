<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-warm-200/60">
    <nav class="w-full px-6 sm:px-8 lg:px-12 h-14 flex items-center justify-between">
        {{-- Left: Profile --}}
        <div class="flex items-center gap-2.5">
            @auth
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-warm-700 hidden sm:block">Welcome, {{ Auth::user()->name ?? 'User' }}</span>
                </a>
            @else
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center">
                        <svg class="w-4 h-4 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-warm-400 hidden sm:block">Guest</span>
                </div>
            @endauth
        </div>

        {{-- Right: Nav links + Auth --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="text-sm font-medium text-warm-500 hover:text-warm-700 transition-colors">Home</a>

            @auth
                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-warm-500 hover:text-warm-700 transition-colors hidden sm:block">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-warm-500 hover:text-warm-700 transition-colors">Log out</button>
                </form>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm font-medium bg-physical-100 hover:bg-physical-200 text-physical-700 px-4 py-1.5 rounded-full transition-colors">Log in</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-mental-100 hover:bg-mental-200 text-mental-700 px-4 py-1.5 rounded-full transition-colors">Sign up</a>
                @endif
            @endauth
        </div>
    </nav>
</header>
