@php
    // Accept data passed from openModalWithData()
    $modalData = $modalData ?? [];
    $concerns = (array) ($modalData['concerns'] ?? []);
    $presentSymptoms = (array) ($modalData['presentSymptoms'] ?? []);
    $type = $modalData['type'] ?? 'physical';
    $whyThinking = $modalData['whyThinking'] ?? '';
    $frequency = $modalData['frequency'] ?? '';
    $impact = (array) ($modalData['impact'] ?? []);
    $interference = $modalData['interference'] ?? '';
    $talkedTo = $modalData['talkedTo'] ?? '';
    $tried = (array) ($modalData['tried'] ?? []);
@endphp

<div id="modal-neurodivergent" class="hidden">
    <div class="rounded-2xl bg-white shadow-2xl">
        <div class="p-5 border-b border-warm-200 flex items-center justify-between">
            <h3 class="font-display font-bold text-warm-800 text-lg">What could this relate to?</h3>
            <button type="button" onclick="closeModal();" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-5 space-y-4 text-sm text-warm-600 leading-relaxed">
            <p class="font-medium text-warm-700">Based on what you shared, here's what some neurodevelopmental and neurological traits could relate to what you're experiencing.</p>
            <p><strong class="text-warm-700">This is not a diagnosis.</strong> It's just another lens on what you're going through. Having these traits doesn't conflict with being physically unwell — they can show up together.</p>

            {{-- Personalised recap --}}
            <div class="bg-warm-50 border border-warm-200 rounded-xl p-4 space-y-1.5">
                <p class="text-xs text-warm-500 font-semibold uppercase tracking-wider mb-2">What you told us</p>
                @if($type === 'physical' && count($concerns) > 0)
                    <p class="text-warm-700"><strong>Concerns:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), $concerns)) }}</p>
                @endif
                @if(count($presentSymptoms) > 0)
                    <p class="text-warm-700"><strong>Additional symptoms:</strong> {{ implode(', ', array_map(fn($s) => ucfirst(str_replace('-', ' ', $s)), $presentSymptoms)) }}</p>
                @endif
                @if($frequency)
                    <p class="text-warm-700"><strong>Frequency:</strong> {{ ucfirst(str_replace('-', ' ', $frequency)) }}</p>
                @endif
                @if(count($impact) > 0)
                    <p class="text-warm-700"><strong>Areas affected:</strong> {{ implode(', ', array_map(fn($i) => ucfirst(str_replace('-', ' ', $i)), $impact)) }}</p>
                @endif
                @if($interference)
                    <p class="text-warm-700"><strong>How much it gets in the way:</strong> {{ $interference }}/5</p>
                @endif
                @if($talkedTo)
                    <p class="text-warm-700"><strong>Have you spoken to anyone:</strong> {{ ucfirst(str_replace('_', ' ', $talkedTo)) }}</p>
                @endif
                @if(count($tried) > 0)
                    <p class="text-warm-700"><strong>Things you've tried:</strong> {{ implode(', ', array_map(fn($t) => ucfirst(str_replace('-', ' ', $t)), $tried)) }}</p>
                @endif
                @if($whyThinking)
                    <p class="text-warm-700"><strong>Why you think this:</strong> "{{ $whyThinking }}"</p>
                @endif
            </div>

            <p></p>
            <p>Pick the ones that resonate with you:</p>

            <div class="space-y-2">
                @php
                $options = [
                    ['value' => 'sensory', 'label' => 'Sensory sensitivities', 'desc' => 'Bright lights, loud sounds, certain textures, or smells bother you a lot',
                        'relates' => 'Often overlaps with chronic overstimulation, migraines, tension, and a heightened stress response. Can make physical symptoms feel more intense.'],
                    ['value' => 'dopamine', 'label' => 'Highs and lows around interest', 'desc' => 'You can hyperfocus on some things and can barely get started on others',
                        'relates' => 'Commonly shows up alongside burnout, sleep disruption, appetite swings, and the kind of fatigue that doesn\'t improve with rest. Also linked to mood variability.'],
                    ['value' => 'burnout', 'label' => 'Burnout from masking or overexerting', 'desc' => 'You hold it together fine most of the time, but then crash hard',
                        'relates' => 'Can look like depression, chronic fatigue, tension symptoms (headaches, stomach issues, tight muscles), and a weakened immune response — getting sick more easily.'],
                    ['value' => 'sleep', 'label' => 'Sleep rhythm issues', 'desc' => 'Your body clock feels off — night owl, delayed sleep, or irregular',
                        'relates' => 'Sleep rhythm issues often travel with mood changes, digestive problems, hormonal fluctuations, and trouble regulating energy — sometimes mistaken for other conditions.'],
                    ['value' => 'stims', 'label' => 'Repetitive movements or habits', 'desc' => 'Fidgeting, pacing, humming, rocking, or other stims when stressed',
                        'relates' => 'Stimming is usually a self-regulation response to overload, anxiety, or adrenaline. Can co-occur with anxiety disorders, sensory processing differences, and chronic stress.'],
                    ['value' => 'routines', 'label' => 'Routines feel non-negotiable', 'desc' => 'Small changes in plan throw you off more than they should',
                        'relates' => 'Often tied to anxiety, a need for predictability under stress, or a nervous system that\'s stuck in high-alert mode. Disruption can trigger real physical stress responses.'],
                    ['value' => 'not-sure', 'label' => 'Unsure / want to know more', 'desc' => 'You don\'t know — but curious',
                        'relates' => 'Totally fine. Neurodivergence is a lens, not a box. If any of this rings a bell later, it is worth reading up from autistic and ADHD-led sources rather than just armchair diagnosis threads.'],
                ];
                @endphp
                @foreach($options as $o)
                <label class="block cursor-pointer">
                    <input type="checkbox" name="neurodivergence[]" value="{{ $o['value'] }}" data-relates="{{ $o['relates'] }}" class="peer hidden">
                    <div class="flex items-start gap-3 p-3 rounded-xl border-2 border-warm-200 bg-white peer-checked:border-mental-400 peer-checked:bg-mental-50 hover:border-warm-300 transition-all duration-200">
                        <div class="w-4 h-4 rounded border-2 border-warm-200 peer-checked:border-mental-500 shrink-0 flex items-center justify-center transition-colors mt-0.5">
                            <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-warm-800">{{ $o['label'] }}</div>
                            <div class="text-xs text-warm-500 mb-1">{{ $o['desc'] }}</div>
                            <div class="text-xs text-warm-400 italic border-l-2 border-warm-200 pl-2">{{ $o['relates'] }}</div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="px-5 pb-5 flex justify-end gap-2">
            <button type="button" onclick="closeModal();" class="px-4 py-2 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">Close</button>
            <button type="button" onclick="saveNeurodivergence()" class="px-4 py-2 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-medium transition-colors">Save my picks</button>
        </div>
    </div>
</div>