@extends('layouts.app')

@section('content')

@if(Auth::check())
    {{-- ============ LOGGED IN HOMEPAGE ============ --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-physical-50/40 via-white to-white"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-gradient-to-br from-emerald-200/20 via-white/30 to-sky-200/20 rounded-full blur-3xl -z-0"></div>

        <div class="relative w-full px-6 sm:px-8 lg:px-12 pt-16 sm:pt-20 pb-14">
            <div class="w-full animate-fade-in">
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-warm-800 mb-3 tracking-tight">
                    Welcome back, {{ Auth::user()->name ?? 'there' }}
                </h1>
                <p class="text-warm-500 text-lg max-w-xl mb-10">Ready to check in on how you're doing?</p>

                {{-- Quick actions --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
                    <a href="{{ route('physical.index') }}" class="group flex items-center gap-4 bg-white rounded-2xl border-2 border-physical-200 p-5 card-hover">
                        <div class="w-12 h-12 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-warm-800">Physical</div>
                            <div class="text-sm text-warm-500">Start a new check</div>
                        </div>
                    </a>
                    <a href="{{ route('mental.index') }}" class="group flex items-center gap-4 bg-white rounded-2xl border-2 border-mental-200 p-5 card-hover">
                        <div class="w-12 h-12 rounded-xl bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-warm-800">Mental</div>
                            <div class="text-sm text-warm-500">Check in on feelings</div>
                        </div>
                    </a>
                    <a href="{{ route('other.index') }}" class="group flex items-center gap-4 bg-white rounded-2xl border-2 border-other-200 p-5 card-hover">
                        <div class="w-12 h-12 rounded-xl bg-other-100 border border-other-200 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-warm-800">Not sure?</div>
                            <div class="text-sm text-warm-500">Figure it out together</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Recent History --}}
    <div class="w-full px-6 sm:px-8 lg:px-12 pb-16">
        <div class="w-full">
            <h2 class="font-display font-bold text-xl text-warm-800 mb-4">Recent activity</h2>
            <div class="bg-white rounded-2xl border border-warm-200 p-8 text-center">
                <div class="w-12 h-12 rounded-xl bg-warm-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-warm-500 text-sm mb-4">No recent screenings yet. Start one above to see your history here.</p>
                <a href="{{ route('physical.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-physical-600 hover:text-physical-700 transition-colors">
                    Start your first screening
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>

@else
    {{-- ============ GUEST HOMEPAGE ============ --}}

    {{-- Hero Section --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-physical-50/40 via-white to-white"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-gradient-to-br from-emerald-200/20 via-white/30 to-sky-200/20 rounded-full blur-3xl -z-0"></div>
        <div class="absolute top-20 right-[8%] w-72 h-72 bg-physical-200/20 rounded-full blur-3xl"></div>
        <div class="absolute top-40 left-[5%] w-56 h-56 bg-mental-200/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 right-[15%] w-64 h-64 bg-other-200/15 rounded-full blur-3xl"></div>

        <div class="relative w-full px-6 sm:px-8 lg:px-12 pt-16 sm:pt-20 pb-14">
            <div class="w-full flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                <div class="flex-1 animate-fade-in">
                    <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm border border-warm-200 rounded-full px-4 py-1.5 mb-6 shadow-sm">
                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center shadow-sm border border-warm-200">
                            <span class="text-warm-800 font-bold text-[9px]">T</span>
                        </div>
                        <span class="text-xs font-medium text-warm-500">A preliminary symptom checker</span>
                    </div>

                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl text-warm-800 mb-6 tracking-tight leading-[1.1]">
                        Something feels off.<br>
                        <span class="rainbow-text animate-rainbow">Let's figure out what.</span>
                    </h1>

                    <p class="text-warm-500 text-lg sm:text-xl leading-relaxed max-w-xl mb-8">
                        {{ config('app.name', 'Tweek') }} helps you understand whether what you're experiencing is worth a trip to the doctor — or if it's something you can chill about. No diagnoses, no scare tactics, just honest guidance.
                    </p>

                    <div class="flex flex-wrap gap-4 text-sm text-warm-500 mb-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Free to use
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            No sign-up required
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Your data stays on your device
                        </div>
                    </div>

                    @if (Route::has('login'))
                    <div class="flex items-center gap-2 text-xs text-warm-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <a href="{{ route('login') }}" class="underline underline-offset-2 hover:text-warm-600 transition-colors">Sign in</a> for screening history, saved results, and personalized recommendations
                    </div>
                    @endif
                </div>

                <div class="flex-1 max-w-lg lg:max-w-none animate-fade-in" style="animation-delay: 0.15s;">
                    @if($brand['hero_image_path'] ?? '')
                        <div class="relative w-full aspect-[3/2] max-w-lg mx-auto rounded-3xl overflow-hidden shadow-xl border border-warm-200">
                            <img src="{{ Storage::disk('public')->url($brand['hero_image_path']) }}" alt="{{ $brand['name'] }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="relative w-full aspect-square max-w-md mx-auto">
                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#8CD9A0;stop-opacity:0.3"/>
                                        <stop offset="100%" style="stop-color:#96C8FF;stop-opacity:0.3"/>
                                    </linearGradient>
                                    <linearGradient id="grad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#BEABFF;stop-opacity:0.25"/>
                                        <stop offset="100%" style="stop-color:#FFDF85;stop-opacity:0.25"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="200" cy="180" rx="160" ry="140" fill="url(#grad1)"/>
                                <ellipse cx="160" cy="240" rx="120" ry="100" fill="url(#grad2)"/>
                            </svg>
                            <div class="absolute top-[15%] left-[20%] w-12 h-12 rounded-2xl bg-physical-100 border border-physical-200 flex items-center justify-center shadow-sm animate-pulse-soft">
                                <svg class="w-6 h-6 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div class="absolute top-[10%] right-[15%] w-11 h-11 rounded-2xl bg-mental-100 border border-mental-200 flex items-center justify-center shadow-sm animate-pulse-soft" style="animation-delay: 0.5s;">
                                <svg class="w-5 h-5 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            <div class="absolute bottom-[20%] left-[10%] w-10 h-10 rounded-2xl bg-other-100 border border-other-200 flex items-center justify-center shadow-sm animate-pulse-soft" style="animation-delay: 1s;">
                                <svg class="w-5 h-5 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="absolute bottom-[30%] right-[8%] w-14 h-14 rounded-2xl bg-white border border-warm-200 flex items-center justify-center shadow-md animate-pulse-soft" style="animation-delay: 1.5s;">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center">
                                    <span class="text-warm-800 font-bold text-sm">T</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="w-full px-6 sm:px-8 lg:px-12 py-14">
        <div class="w-full">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-8 h-8 rounded-lg bg-warm-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h2 class="font-display font-bold text-xl sm:text-2xl text-warm-800">How it works</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 stagger-children">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 text-physical-600 font-bold text-sm">1</div>
                    <div>
                        <h3 class="font-semibold text-warm-800 mb-1">Tell us what's up</h3>
                        <p class="text-sm text-warm-500 leading-relaxed">Pick a category and describe what you're experiencing. No judgment, no rushing.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-xl bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 text-mental-600 font-bold text-sm">2</div>
                    <div>
                        <h3 class="font-semibold text-warm-800 mb-1">Get some perspective</h3>
                        <p class="text-sm text-warm-500 leading-relaxed">We'll help you make sense of what's happening — what it could be and what to consider.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-xl bg-other-100 border border-other-200 flex items-center justify-center shrink-0 text-other-600 font-bold text-sm">3</div>
                    <div>
                        <h3 class="font-semibold text-warm-800 mb-1">Decide what's next</h3>
                        <p class="text-sm text-warm-500 leading-relaxed">Walk away with clarity — whether that means seeing a doc or just having peace of mind.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="w-full px-6 sm:px-8 lg:px-12"><div class="h-px bg-warm-200"></div></div>

    {{-- Category Selection --}}
    <section class="w-full px-6 sm:px-8 lg:px-12 py-14">
        <div class="w-full">
            <div class="text-center mb-12 animate-fade-in">
                <h2 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-3 tracking-tight">What's going on with you?</h2>
                <p class="text-warm-500 text-base sm:text-lg max-w-xl mx-auto">Pick the one that fits best. If you're not sure, that's okay — we've got you.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 stagger-children">
                <a href="{{ route('physical.index') }}" class="group block">
                    <div class="relative bg-white rounded-2xl border-2 border-physical-200 p-6 sm:p-8 text-center card-hover overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-physical-100 rounded-bl-[80px] -z-0 opacity-60 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-physical-50 rounded-full -z-0 opacity-0 group-hover:opacity-60 transition-all duration-500 group-hover:scale-150"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-physical-100 border border-physical-200 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <svg class="w-7 h-7 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <h2 class="font-display font-bold text-xl text-warm-800 mb-2">Physical</h2>
                            <p class="text-warm-500 text-sm leading-relaxed">Fevers, pain, breakouts, or something else your body's doing</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('mental.index') }}" class="group block">
                    <div class="relative bg-white rounded-2xl border-2 border-mental-200 p-6 sm:p-8 text-center card-hover overflow-hidden">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-mental-100 rounded-br-[80px] -z-0 opacity-60 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-mental-50 rounded-full -z-0 opacity-0 group-hover:opacity-60 transition-all duration-500 group-hover:scale-150"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-mental-100 border border-mental-200 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                                <svg class="w-7 h-7 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            <h2 class="font-display font-bold text-xl text-warm-800 mb-2">Mental</h2>
                            <p class="text-warm-500 text-sm leading-relaxed">Thoughts, feelings, or patterns that are weighing on you</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('other.index') }}" class="group block">
                    <div class="relative bg-white rounded-2xl border-2 border-other-200 p-6 sm:p-8 text-center card-hover overflow-hidden">
                        <div class="absolute bottom-0 right-0 w-32 h-32 bg-other-100 rounded-tl-[80px] -z-0 opacity-60 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-other-50 rounded-full -z-0 opacity-0 group-hover:opacity-60 transition-all duration-500 group-hover:scale-150"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-other-100 border border-other-200 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <svg class="w-7 h-7 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h2 class="font-display font-bold text-xl text-warm-800 mb-2">Not sure?</h2>
                            <p class="text-warm-500 text-sm leading-relaxed">Don't know if it's physical, mental, or something else</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <div class="w-full px-6 sm:px-8 lg:px-12"><div class="h-px bg-warm-200"></div></div>

    {{-- Why Tweek --}}
    <section class="w-full px-6 sm:px-8 lg:px-12 py-14">
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in">
                <h2 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-4 tracking-tight">Why {{ config('app.name', 'Tweek') }}?</h2>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                        <div><h3 class="font-semibold text-warm-800 mb-1">No self-diagnosis traps</h3><p class="text-sm text-warm-500 leading-relaxed">We won't tell you "you definitely have X." We'll tell you what could be happening and whether it's worth getting a professional opinion.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <div><h3 class="font-semibold text-warm-800 mb-1">Made for real people</h3><p class="text-sm text-warm-500 leading-relaxed">Whether it's doom-scrolling armchair diagnoses or a genuine concern you've been sitting on, we meet you where you are.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-other-100 border border-other-200 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-other-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                        <div><h3 class="font-semibold text-warm-800 mb-1">Safety first, always</h3><p class="text-sm text-warm-500 leading-relaxed">If things ever get serious, we'll point you toward real help — because that's the responsible thing to do.</p></div>
                    </div>
                </div>
            </div>
            <div class="relative animate-fade-in" style="animation-delay: 0.15s;">
                <div class="bg-gradient-to-br from-physical-50 via-mental-50 to-other-50 rounded-3xl p-8 sm:p-10 border border-warm-200">
                    <blockquote class="text-warm-600 leading-relaxed text-sm sm:text-base italic mb-4">"I saw someone on TikTok say that if you fidget you definitely have ADHD. Turns out I was just anxious about my exams."</blockquote>
                    <p class="text-xs text-warm-400">— The kind of thing {{ config('app.name', 'Tweek') }} helps you sort through</p>
                    <div class="mt-6 flex items-center gap-3">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-physical-200 border-2 border-white flex items-center justify-center"><svg class="w-4 h-4 text-physical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                            <div class="w-8 h-8 rounded-full bg-mental-200 border-2 border-white flex items-center justify-center"><svg class="w-4 h-4 text-mental-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
                            <div class="w-8 h-8 rounded-full bg-other-200 border-2 border-white flex items-center justify-center"><svg class="w-4 h-4 text-other-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        </div>
                        <span class="text-xs text-warm-400">3 screening categories</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Bottom CTA --}}
    <section class="w-full px-6 sm:px-8 lg:px-12 pb-16">
        <div class="w-full bg-gradient-to-r from-physical-50 via-mental-50 to-other-50 rounded-3xl p-8 sm:p-12 text-center border border-warm-200 animate-fade-in">
            <h3 class="font-display font-bold text-xl sm:text-2xl text-warm-800 mb-3">Still not sure?</h3>
            <p class="text-warm-500 max-w-lg mx-auto mb-6 leading-relaxed">That's completely fine. Our "Not sure" flow is designed exactly for this — describe what you're feeling and we'll help you figure out which direction to go.</p>
            <a href="{{ route('other.index') }}" class="inline-flex items-center gap-2 bg-warm-800 text-white px-6 py-3 rounded-xl font-semibold hover:bg-warm-700 transition-colors">
                Start with "Not sure"
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </section>

@endif

@endsection

@section('footer')
@include('partials.footer')
@endsection
