@php
    $currentRoute = request()->route()->getName();
@endphp

<aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:top-14 lg:border-r lg:border-warm-200 lg:bg-white/60 lg:backdrop-blur-sm z-30">
    <div class="flex flex-col flex-1 pt-6 pb-4 overflow-y-auto">
        {{-- Navigation --}}
        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ $currentRoute === 'dashboard' ? 'bg-mental-100 text-mental-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Screenings</span>
            </div>

            <a href="{{ route('physical.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'physical') ? 'bg-physical-100 text-physical-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <div class="w-5 h-5 rounded-md bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                Physical
            </a>

            <a href="{{ route('mental.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'mental') ? 'bg-mental-100 text-mental-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <div class="w-5 h-5 rounded-md bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                Mental
            </a>

            <a href="{{ route('other.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'other') ? 'bg-other-100 text-other-700' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <div class="w-5 h-5 rounded-md bg-other-100 border border-other-200 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                Not sure?
            </a>

            @if(Auth::user()->is_admin ?? false)
            <div class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-semibold text-warm-400 uppercase tracking-wider">Admin</span>
            </div>

            <a href="{{ url('/admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                {{ str_starts_with($currentRoute, 'admin') ? 'bg-warm-800 text-white' : 'text-warm-500 hover:text-warm-700 hover:bg-warm-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Admin Panel
            </a>
            @endif
        </nav>

        {{-- Bottom: Logout --}}
        <div class="px-3 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-warm-500 hover:text-warm-700 hover:bg-warm-100 transition-colors w-full">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Log out
                </button>
            </form>
        </div>
    </div>
</aside>
