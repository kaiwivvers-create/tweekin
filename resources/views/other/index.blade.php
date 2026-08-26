@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 sm:px-8 lg:px-12 py-8 sm:py-12">

    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-warm-400 hover:text-warm-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="mb-8 animate-fade-in">
        <div class="w-12 h-12 rounded-xl bg-other-100 border border-other-200 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Let's figure it out together</h1>
        <p class="text-warm-500 leading-relaxed">No idea what's going on? That's completely fine. Tell us what you're experiencing and we'll help you narrow it down.</p>
    </div>

    <div class="bg-other-50 border border-other-200 rounded-2xl p-4 sm:p-5 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
        <div class="flex gap-3">
            <div class="w-8 h-8 rounded-lg bg-other-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-sm text-other-700 leading-relaxed">
                <p class="font-semibold mb-1">Not sure if it's physical or mental?</p>
                <p>Sometimes it's both, sometimes it's neither. Just describe what you're feeling and we'll help you figure out which path makes the most sense — or suggest seeing someone who can.</p>
            </div>
        </div>
    </div>

    <form id="other-form" action="{{ route('other.results') }}" method="GET" class="space-y-6">

        <div class="animate-fade-in" style="animation-delay: 0.2s;">
            <label for="description" class="block text-sm font-semibold text-warm-700 mb-2">What are you experiencing? Describe it however makes sense to you.</label>
            <textarea id="description" name="description" rows="4" placeholder="e.g., 'I feel tired all the time but also kind of anxious' or 'My chest feels tight sometimes' or 'I just feel off and I can't explain it'" class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 placeholder-warm-400 focus:border-other-400 focus:ring-0 resize-none transition-colors" required></textarea>
        </div>

        <div class="animate-fade-in" style="animation-delay: 0.3s;">
            <label class="block text-sm font-semibold text-warm-700 mb-3">If you had to guess, does it feel more...</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="block cursor-pointer">
                    <input type="radio" name="lean" value="physical" class="peer hidden" required>
                    <div class="p-4 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 transition-all duration-200">
                        <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center mx-auto mb-1">
                            <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <div class="text-sm font-medium text-warm-600 peer-checked:text-physical-700">Physical</div>
                    </div>
                </label>
                <label class="block cursor-pointer">
                    <input type="radio" name="lean" value="both" class="peer hidden">
                    <div class="p-4 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-other-400 peer-checked:bg-other-50 hover:border-warm-300 transition-all duration-200">
                        <div class="w-8 h-8 rounded-lg bg-other-100 border border-other-200 flex items-center justify-center mx-auto mb-1">
                            <svg class="w-4 h-4 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-sm font-medium text-warm-600 peer-checked:text-other-700">Both / Not sure</div>
                    </div>
                </label>
                <label class="block cursor-pointer">
                    <input type="radio" name="lean" value="mental" class="peer hidden">
                    <div class="p-4 rounded-xl border-2 border-warm-200 bg-white text-center peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                        <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-1">
                            <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div class="text-sm font-medium text-warm-600 peer-checked:text-mental-700">Mental</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="animate-fade-in" style="animation-delay: 0.4s;">
            <label class="block text-sm font-semibold text-warm-700 mb-3">How long has this been going on?</label>
            <div class="grid grid-cols-2 gap-2">
                <label class="block cursor-pointer"><input type="radio" name="duration" value="today" class="peer hidden" required><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-other-400 peer-checked:bg-other-50 peer-checked:text-other-700 hover:border-warm-300 transition-all duration-200">Just started today</div></label>
                <label class="block cursor-pointer"><input type="radio" name="duration" value="few-days" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-other-400 peer-checked:bg-other-50 peer-checked:text-other-700 hover:border-warm-300 transition-all duration-200">A few days</div></label>
                <label class="block cursor-pointer"><input type="radio" name="duration" value="weeks" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-other-400 peer-checked:bg-other-50 peer-checked:text-other-700 hover:border-warm-300 transition-all duration-200">A few weeks</div></label>
                <label class="block cursor-pointer"><input type="radio" name="duration" value="months" class="peer hidden"><div class="p-3 rounded-xl border-2 border-warm-200 bg-white text-center text-sm font-medium text-warm-600 peer-checked:border-other-400 peer-checked:bg-other-50 peer-checked:text-other-700 hover:border-warm-300 transition-all duration-200">Months or longer</div></label>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-3 px-6 rounded-xl bg-other-400 hover:bg-other-500 text-white font-semibold transition-colors duration-200">Help me understand</button>
        </div>
    </form>
</div>
@endsection
