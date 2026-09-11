@extends('layouts.app')

@section('content')
<div class="w-full flex">
    {{-- Sidebar with vertical progress --}}
    <div class="hidden lg:flex w-24 shrink-0 border-r border-physical-100 bg-physical-50/30 py-12 justify-center sticky top-14 h-[calc(100vh-3.5rem)]">
        @include('components.vertical-progress', ['steps' => ['Category', 'Questions', 'Details', 'Results'], 'current' => 1, 'theme' => 'physical'])
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
                <span class="text-xs font-medium text-physical-600">Physical symptoms</span>
                <span class="text-xs text-warm-400">Step 1 of 4</span>
            </div>
            <div class="h-1.5 bg-physical-100 rounded-full overflow-hidden">
                <div class="h-full bg-physical-400 rounded-full progress-bar" style="width: 25%"></div>
            </div>
        </div>

        <div class="mb-8 animate-fade-in">
            <div class="w-12 h-12 rounded-xl bg-physical-100 border border-physical-200 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">What do you think it might be?</h1>
            <p class="text-warm-500 leading-relaxed">Pick all that apply, or go with "Not sure" if you'd rather describe it yourself. You can select multiple.</p>
        </div>

        <form id="physical-form" action="{{ route('physical.questions') }}" method="GET" class="space-y-3 stagger-children">
            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="fever" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Fever or chills</div>
                        <div class="text-sm text-warm-500">Elevated temperature, sweating, feeling cold</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="pain" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Pain or discomfort</div>
                        <div class="text-sm text-warm-500">Headache, body aches, stomach pain, etc.</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="skin" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Skin issues</div>
                        <div class="text-sm text-warm-500">Breakouts, rashes, redness, irritation</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="respiratory" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Breathing issues</div>
                        <div class="text-sm text-warm-500">Shortness of breath, coughing, wheezing</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="digestive" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Stomach or digestion</div>
                        <div class="text-sm text-warm-500">Nausea, diarrhea, bloating, appetite changes</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="fatigue" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Fatigue or energy</div>
                        <div class="text-sm text-warm-500">Constant tiredness, low energy, weakness</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="other" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Something else</div>
                        <div class="text-sm text-warm-500">Doesn't fit the above categories</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <label class="block cursor-pointer">
                <input type="checkbox" name="symptom[]" value="unsure" class="peer hidden">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-physical-400 peer-checked:bg-physical-50 hover:border-warm-300 hover:bg-warm-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-physical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-warm-800">Not sure</div>
                        <div class="text-sm text-warm-500">You're not sure what's going on — that's okay!</div>
                    </div>
                    <div class="w-5 h-5 rounded border-2 border-physical-200 peer-checked:border-physical-500 shrink-0 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </label>

            <div id="not-sure-input" class="hidden animate-fade-in">
                <label for="custom-symptom" class="block text-sm font-medium text-warm-600 mb-2">Can you describe what you're feeling? (optional)</label>
                <textarea id="custom-symptom" name="custom_symptom" rows="3" placeholder="e.g., 'My left knee hurts when I walk up stairs' or 'I've been feeling dizzy for a few days'" class="w-full px-4 py-3 rounded-xl border-2 border-warm-200 bg-white text-warm-800 placeholder-warm-400 focus:border-physical-400 focus:ring-0 resize-none transition-colors"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-6 rounded-xl bg-physical-400 hover:bg-physical-500 text-white font-semibold transition-colors duration-200 disabled:opacity-40 disabled:cursor-not-allowed" disabled id="physical-submit">Continue</button>
            </div>
        </form>
    </div>
</div>

<script>
    const checkboxes = document.querySelectorAll('input[name="symptom[]"]');
    const submitBtn = document.getElementById('physical-submit');
    const notSureInput = document.getElementById('not-sure-input');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const anyChecked = [...checkboxes].some(c => c.checked);
            submitBtn.disabled = !anyChecked;

            // Show/hide "not sure" description input
            const unsureChecked = [...checkboxes].find(c => c.value === 'unsure')?.checked;
            notSureInput.classList.toggle('hidden', !unsureChecked);
        });
    });
</script>
@endsection
