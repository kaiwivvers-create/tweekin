<footer class="border-t border-warm-200 bg-white/50 mt-16">
    <div class="w-full px-6 sm:px-8 lg:px-12 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-8">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center shadow-sm border border-warm-200">
                        <span class="text-warm-800 font-bold text-xs">T</span>
                    </div>
                    <span class="font-display font-bold text-base text-warm-800">{{ config('app.name', 'Tweek') }}</span>
                </div>
                <p class="text-sm text-warm-500 leading-relaxed max-w-xs">A preliminary symptom checker. Not a diagnosis — just a starting point to help you figure out what's next.</p>
            </div>

            {{-- Links --}}
            <div>
                <h4 class="font-semibold text-warm-700 mb-3 text-sm">Quick links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('physical.index') }}" class="text-warm-500 hover:text-warm-700 transition-colors">Physical symptoms</a></li>
                    <li><a href="{{ route('mental.index') }}" class="text-warm-500 hover:text-warm-700 transition-colors">Mental health</a></li>
                    <li><a href="{{ route('other.index') }}" class="text-warm-500 hover:text-warm-700 transition-colors">Not sure?</a></li>
                    <li><button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'disclaimer' }))" class="text-warm-500 hover:text-warm-700 transition-colors">Disclaimer</button></li>
                </ul>
            </div>

            {{-- Social --}}
            <div>
                <h4 class="font-semibold text-warm-700 mb-3 text-sm">Connect with us</h4>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-lg bg-warm-100 hover:bg-warm-200 flex items-center justify-center transition-colors" aria-label="Instagram">
                        <svg class="w-4 h-4 text-warm-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-warm-100 hover:bg-warm-200 flex items-center justify-center transition-colors" aria-label="Twitter / X">
                        <svg class="w-4 h-4 text-warm-500" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-warm-100 hover:bg-warm-200 flex items-center justify-center transition-colors" aria-label="TikTok">
                        <svg class="w-4 h-4 text-warm-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-warm-100 hover:bg-warm-200 flex items-center justify-center transition-colors" aria-label="GitHub">
                        <svg class="w-4 h-4 text-warm-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-warm-200 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-warm-400">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Tweek') }}. For informational purposes only.</span>
            <div class="flex items-center gap-4">
                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'privacy' }))" class="hover:text-warm-600 transition-colors">Privacy</button>
                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'terms' }))" class="hover:text-warm-600 transition-colors">Terms</button>
                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'about' }))" class="hover:text-warm-600 transition-colors">About</button>
            </div>
        </div>
    </div>
</footer>
