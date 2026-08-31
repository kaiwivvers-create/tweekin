<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-warm-200/60" x-data="{ open: false, dark: localStorage.getItem('theme') === 'dark' }" x-init="$watch('dark', v => { document.documentElement.classList.toggle('dark', v); localStorage.setItem('theme', v ? 'dark' : 'light') }); if (dark) document.documentElement.classList.add('dark')">
    <nav class="w-full px-6 sm:px-8 lg:px-12 h-14 flex items-center justify-between">
        {{-- Left: Profile --}}
        <div class="flex items-center gap-2.5">
            @auth
                <button onclick="openModal('profile')" class="flex items-center gap-2.5 group">
                    @if(Auth::user()->avatar)
                        <img id="header-avatar" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                             class="w-8 h-8 rounded-full object-cover border-2 border-mental-200 shadow-sm group-hover:scale-105 transition-transform">
                    @else
                        <div id="header-avatar" class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif
                    <span class="text-sm font-medium text-warm-700 hidden sm:block">Welcome, {{ Auth::user()->name ?? 'User' }}</span>
                </button>
            @else
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center">
                        <svg class="w-4 h-4 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-warm-400 hidden sm:block">Guest</span>
                </div>
            @endauth
        </div>

        {{-- Right: Desktop nav --}}
        <div class="hidden sm:flex items-center gap-4">
            <button @click="dark = !dark" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-warm-100 transition-colors" :title="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="!dark" class="w-4 h-4 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-show="dark" x-cloak class="w-4 h-4 text-mental-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>
            <a href="{{ route('home') }}" class="text-sm font-medium text-warm-500 hover:text-warm-700 transition-colors">Home</a>

            @auth
                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-warm-500 hover:text-warm-700 transition-colors">Dashboard</a>
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

        {{-- Mobile hamburger --}}
        <button @click="open = !open" class="sm:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-warm-100 transition-colors">
            <svg x-show="!open" class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="open" x-cloak class="w-5 h-5 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </nav>

    {{-- Mobile dropdown --}}
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="open = false"
         class="sm:hidden border-t border-warm-100 bg-white px-6 py-3 space-y-1">
        <button @click="dark = !dark" class="flex items-center gap-2 w-full py-2 text-sm font-medium text-warm-600 hover:text-warm-800 rounded-lg hover:bg-warm-50 transition-colors">
            <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="dark" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span x-text="dark ? 'Light mode' : 'Dark mode'"></span>
        </button>
        <a href="{{ route('home') }}" class="block py-2 text-sm font-medium text-warm-600 hover:text-warm-800 rounded-lg hover:bg-warm-50 transition-colors">Home</a>

        @auth
            <a href="{{ url('/dashboard') }}" class="block py-2 text-sm font-medium text-warm-600 hover:text-warm-800 rounded-lg hover:bg-warm-50 transition-colors">Dashboard</a>
            <button onclick="openModal('profile'); open = false" class="block w-full text-left py-2 text-sm font-medium text-warm-600 hover:text-warm-800 rounded-lg hover:bg-warm-50 transition-colors">Profile</button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left py-2 text-sm font-medium text-warm-600 hover:text-warm-800 rounded-lg hover:bg-warm-50 transition-colors">Log out</button>
            </form>
        @else
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="block py-2 text-sm font-medium text-physical-600 hover:text-physical-800 rounded-lg hover:bg-physical-50 transition-colors">Log in</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block py-2 text-sm font-medium text-mental-600 hover:text-mental-800 rounded-lg hover:bg-mental-50 transition-colors">Sign up</a>
            @endif
        @endauth
    </div>
</header>
