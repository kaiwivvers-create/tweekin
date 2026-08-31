<template id="modal-resources">
    <div class="p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-display font-bold text-lg text-warm-800">Resources & Support</h3>
            <button onclick="closeModal()" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-4 text-sm">
            {{-- Crisis --}}
            <div class="bg-danger/5 border border-danger/20 rounded-xl p-4">
                <h4 class="font-semibold text-danger mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    In crisis right now?
                </h4>
                <ul class="space-y-1.5 text-warm-600">
                    <li><strong>988 Suicide & Crisis Lifeline:</strong> Call or text <strong>988</strong></li>
                    <li><strong>Crisis Text Line:</strong> Text HOME to <strong>741741</strong></li>
                    <li><strong>Emergency:</strong> Call <strong>911</strong></li>
                </ul>
            </div>

            {{-- Find help --}}
            <div>
                <h4 class="font-semibold text-warm-700 mb-2">Finding professional help</h4>
                <ul class="space-y-1.5 text-warm-500">
                    <li><a href="https://www.psychologytoday.com/us/therapists" target="_blank" class="text-mental-500 hover:underline">Psychology Today Therapist Directory</a> — filter by location, insurance, and specialty</li>
                    <li><a href="https://www.samhsa.gov/find-help" target="_blank" class="text-mental-500 hover:underline">SAMHSA Helpline</a> — 1-800-662-4357 (free, 24/7)</li>
                    <li><a href="https://www.nami.org/help" target="_blank" class="text-mental-500 hover:underline">NAMI HelpLine</a> — 1-800-950-6264</li>
                </ul>
            </div>

            {{-- Learn more --}}
            <div>
                <h4 class="font-semibold text-warm-700 mb-2">Learn more</h4>
                <ul class="space-y-1.5 text-warm-500">
                    <li><a href="https://www.nimh.nih.gov/health" target="_blank" class="text-mental-500 hover:underline">NIMH</a> — reliable info on mental health conditions</li>
                    <li><a href="https://www.mayoclinic.org/diseases-conditions" target="_blank" class="text-mental-500 hover:underline">Mayo Clinic</a> — symptoms, causes, and treatment info</li>
                    <li><a href="https://www.nhs.uk/mental-health/" target="_blank" class="text-mental-500 hover:underline">NHS Mental Health</a> — practical guides and self-help</li>
                </ul>
            </div>

            <p class="text-xs text-warm-400 italic pt-2 border-t border-warm-100">Remember: seeking help is a sign of strength, not weakness. You deserve support.</p>
        </div>
    </div>
</template>
