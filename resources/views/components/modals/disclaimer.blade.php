<template id="modal-disclaimer">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-lg text-warm-800">Important Disclaimer</h3>
            <button onclick="closeModal()" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="space-y-4 text-sm text-warm-600 leading-relaxed">
            <div class="bg-mental-50 border border-mental-200 rounded-xl p-4">
                <p class="font-semibold text-mental-700 mb-2">Tweek is not a medical professional.</p>
                <p>This tool is designed for informational and preliminary screening purposes only. It cannot and should not replace consultation with a qualified healthcare provider.</p>
            </div>
            <div class="bg-physical-50 border border-physical-200 rounded-xl p-4">
                <p class="font-semibold text-physical-700 mb-2">What this tool does:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Helps you organize your thoughts about symptoms</li>
                    <li>Provides general information about what you might be experiencing</li>
                    <li>Indicates whether professional evaluation may be appropriate</li>
                </ul>
            </div>
            <div class="bg-danger/10 border border-danger/20 rounded-xl p-4">
                <p class="font-semibold text-danger mb-2">In case of emergency:</p>
                <p>If you are experiencing a medical emergency, please call emergency services immediately (911 in the US) or go to your nearest emergency room.</p>
            </div>
            <p class="text-warm-400 text-xs">By using Tweek, you acknowledge that this is not a substitute for professional medical advice, diagnosis, or treatment.</p>
        </div>
    </div>
</template>
