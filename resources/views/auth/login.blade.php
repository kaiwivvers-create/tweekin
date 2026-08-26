<x-guest-layout>
    <div class="mb-8">
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Welcome back</h1>
        <p class="text-sm text-warm-500">Log in to access your screening history and saved results.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-warm-300 text-mental-500 focus:ring-mental-400" name="remember">
                <span class="ms-2 text-sm text-warm-600">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-warm-500 hover:text-warm-700 transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif

            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-mental-400 hover:bg-mental-500 border border-transparent rounded-xl font-semibold text-sm text-white transition-colors duration-200">
                Log in
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-warm-200 text-center">
        <p class="text-sm text-warm-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-mental-600 hover:text-mental-700 transition-colors">Sign up</a>
        </p>
    </div>
</x-guest-layout>
