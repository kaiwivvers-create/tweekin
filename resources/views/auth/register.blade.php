<x-guest-layout>
    <div class="mb-8">
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Create an account</h1>
        <p class="text-sm text-warm-500">Sign up to save your screening history and get personalized recommendations.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Create a password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-mental-400 hover:bg-mental-500 border border-transparent rounded-xl font-semibold text-sm text-white transition-colors duration-200">
                Create account
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-warm-200 text-center">
        <p class="text-sm text-warm-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-physical-600 hover:text-physical-700 transition-colors">Log in</a>
        </p>
    </div>
</x-guest-layout>
