<template id="modal-about">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-lg text-warm-800">About {{ config('app.name', 'Tweek') }}</h3>
            <button onclick="closeModal()" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="space-y-4 text-sm text-warm-600 leading-relaxed">
            <div class="text-center py-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 via-white to-sky-500 flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <span class="text-warm-800 font-bold text-2xl">T</span>
                </div>
                <h4 class="font-display font-bold text-xl text-warm-800 mb-1">{{ config('app.name', 'Tweek') }}</h4>
                <p class="text-warm-400 text-xs">A preliminary symptom checker</p>
            </div>
            <div>
                <h4 class="font-semibold text-warm-700 mb-2">What is {{ config('app.name', 'Tweek') }}?</h4>
                <p>{{ config('app.name', 'Tweek') }} is a tool designed to help you understand whether your symptoms might warrant professional attention. It's not a diagnostic tool — think of it more like a friendly compass that helps you figure out if you should see a doctor.</p>
            </div>
            <div>
                <h4 class="font-semibold text-warm-700 mb-2">How does it work?</h4>
                <p>You tell {{ config('app.name', 'Tweek') }} what you're experiencing, and it helps organize that information and provides general guidance on whether professional evaluation might be appropriate.</p>
            </div>
            <div>
                <h4 class="font-semibold text-warm-700 mb-2">For mental health</h4>
                <p>Sometimes you just need to talk. {{ config('app.name', 'Tweek') }} can listen and help you understand what you're feeling — without judgment, without armchair diagnosis, and without telling you what you "have" based on a checklist.</p>
            </div>
            <p class="text-warm-400 text-xs italic">{{ config('app.name', 'Tweek') }} is currently in development. Features and content are subject to change.</p>
        </div>
    </div>
</template>
