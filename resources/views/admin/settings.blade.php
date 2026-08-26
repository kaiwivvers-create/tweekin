@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">App Settings</h1>
        <p class="text-sm text-warm-500">Configure your application settings.</p>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/30 text-success rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl border border-warm-200 p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-warm-700 mb-1.5">App Name</label>
            <input type="text" name="app_name" value="{{ config('app.name', 'Tweek') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors" required>
            <p class="text-xs text-warm-400 mt-1">This appears in the browser tab, emails, and throughout the app.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-warm-700 mb-1.5">Disclaimer Text</label>
            <textarea name="disclaimer_text" rows="3" class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors resize-none" placeholder="This is not a diagnostic tool...">{{ old('disclaimer_text') }}</textarea>
            <p class="text-xs text-warm-400 mt-1">Shown at the top of every page.</p>
        </div>

        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-mental-400 hover:bg-mental-500 border border-transparent rounded-xl font-semibold text-sm text-white transition-colors duration-200">
            Save settings
        </button>
    </form>
</div>
@endsection
