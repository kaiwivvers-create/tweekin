@extends('layouts.app')

@section('content')
<div class="w-full flex">
    {{-- Sidebar with vertical progress --}}
    <div class="hidden lg:flex w-24 shrink-0 border-r border-warm-200 bg-white/50 py-12 justify-center sticky top-14 h-[calc(100vh-3.5rem)]">
        @include('components.vertical-progress', ['steps' => ['Details', 'Mode', 'Results'], 'current' => 1, 'theme' => 'mental'])
    </div>

    {{-- Main content --}}
    <div class="flex-1 px-6 sm:px-8 lg:px-12 py-8 sm:py-12">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        {{-- Mobile progress bar --}}
        <div class="lg:hidden mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-mental-600">Mental health check-in</span>
                <span class="text-xs text-warm-400">Step 1 of 3</span>
            </div>
            <div class="h-1.5 bg-mental-100 rounded-full overflow-hidden">
                <div class="h-full bg-mental-400 rounded-full progress-bar" style="width: 33%"></div>
            </div>
        </div>

        <div class="mb-8 animate-fade-in">
            <div class="w-12 h-12 rounded-xl bg-mental-100 border border-mental-200 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">What's on your mind?</h1>
            <p class="text-warm-500 leading-relaxed">No judgment here. Just tell us what you've been thinking or feeling — and if you're not sure, that's totally fine.</p>
        </div>

        <div class="bg-mental-50 border border-mental-200 rounded-2xl p-4 sm:p-5 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-mental-100 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-sm text-mental-700 leading-relaxed">
                    <p class="font-semibold mb-1">A quick heads up:</p>
                    <p>We won't diagnose you or tell you that you have (or don't have) something. We're here to help you understand what you're experiencing and figure out if talking to a professional might help.</p>
                </div>
            </div>
        </div>

        <form id="mental-form" action="{{ route('mental.mode') }}" method="GET" class="space-y-6">
            <input type="hidden" name="concern" value="">

            <div class="animate-fade-in" style="animation-delay: 0.2s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">What do you think might be going on?</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="anxiety" class="peer hidden" required><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Anxiety</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="depression" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Depression</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="stress" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Stress</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="ocd" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">OCD-like thoughts</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="social" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Social issues</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="sleep" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Sleep problems</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="trauma" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Past trauma</div></div></label>
                    <label class="block cursor-pointer"><input type="radio" name="concern_type" value="unsure" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200"><div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1"><svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Not sure</div></div></label>
                </div>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.3s;">
                <label for="why-thinking" class="block text-sm font-semibold text-warm-700 mb-2">Why do you think this might be what's going on?</label>
                <p class="text-xs text-warm-400 mb-3">Did you read something online? Notice patterns? Someone said something? All valid reasons.</p>
                <textarea id="why-thinking" name="why_thinking" rows="4" placeholder="e.g., 'I saw a TikTok about ADHD and it described me perfectly' or 'I've been feeling empty for months'" class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 placeholder-warm-400 focus:border-mental-400 focus:ring-0 resize-none transition-colors" required></textarea>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.4s;">
                <label class="block text-sm font-semibold text-warm-700 mb-3">How long have you felt this way?</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="days" class="peer hidden" required><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-mental-400 peer-checked:bg-mental-50 peer-checked:text-mental-700 hover:border-warm-300 transition-all duration-200">A few days</div></label>
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="weeks" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-mental-400 peer-checked:bg-mental-50 peer-checked:text-mental-700 hover:border-warm-300 transition-all duration-200">A few weeks</div></label>
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="months" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-mental-400 peer-checked:bg-mental-50 peer-checked:text-mental-700 hover:border-warm-300 transition-all duration-200">Months</div></label>
                    <label class="block cursor-pointer"><input type="radio" name="duration" value="years" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-mental-400 peer-checked:bg-mental-50 peer-checked:text-mental-700 hover:border-warm-300 transition-all duration-200">Years / as long as I can remember</div></label>
                </div>
            </div>

            <div class="animate-fade-in" style="animation-delay: 0.5s;">
                <label for="triggers" class="block text-sm font-semibold text-warm-700 mb-2">Is there anything specific that makes it worse or triggers it? (optional)</label>
                <textarea id="triggers" name="triggers" rows="2" placeholder="e.g., 'Being around a lot of people', 'When I'm alone at night', 'Work deadlines'" class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 placeholder-warm-400 focus:border-mental-400 focus:ring-0 resize-none transition-colors"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-6 rounded-xl bg-mental-400 hover:bg-mental-500 text-white font-semibold transition-colors duration-200">How would you like help?</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="concern_type"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelector('input[name="concern"]').value = radio.value;
        });
    });
</script>
@endsection
