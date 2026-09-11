@php
    $type = $type ?? 'general';
    $symptoms = $symptoms ?? [];
    $presentSymptoms = $presentSymptoms ?? [];
    $themeClass = match($type) {
        'physical' => 'physical',
        'mental' => 'mental',
        default => 'other',
    };

    $labels = match($type) {
        'physical' => [
            'fever' => 'Fever or chills',
            'pain' => 'Pain or discomfort',
            'skin' => 'Skin issues',
            'respiratory' => 'Breathing issues',
            'digestive' => 'Stomach or digestion',
            'fatigue' => 'Fatigue or energy',
            'other' => 'Something else',
            'unsure' => 'Not sure',
            'chills' => 'Chills',
            'nausea' => 'Nausea',
            'dizziness' => 'Dizziness',
            'sweating' => 'Sweating',
            'loss-of-appetite' => 'Loss of appetite',
            'swelling' => 'Swelling',
            'redness' => 'Redness',
            'discharge' => 'Discharge',
            'breathing' => 'Difficulty breathing',
        ],
        'mental' => [
            'anxiety' => 'Anxiety',
            'depression' => 'Depression',
            'stress' => 'Stress / burnout',
            'ocd' => 'OCD-like thoughts',
            'social' => 'Social anxiety / issues',
            'sleep' => 'Sleep problems',
            'trauma' => 'Past trauma',
            'unsure' => 'Not sure',
        ],
        default => [],
    };

    // Correlation data: symptom key => what it commonly appears with, why, and a noteworthy flag.
    // This is a COMPARISON of patterns people commonly report together — not a diagnosis.
    $correlations = match($type) {
        'physical' => [
            'fever' => [
                'with' => ['chills', 'sweating', 'fatigue', 'body aches'],
                'why' => 'A fever is the body turning up its thermostat to fight something off. Chills come as it heats up, sweating as it cools down, and the whole process drains energy — so fatigue and aches tend to tag along.',
                'note' => 'A fever above 103°F (39.4°C), or one lasting more than 3 days, is worth getting checked.',
            ],
            'chills' => [
                'with' => ['fever', 'sweating', 'body aches'],
                'why' => 'Chills are how the body raises its temperature — muscles contract to generate heat. When the fever breaks, the opposite happens and sweating kicks in, so chills and sweats often alternate.',
                'note' => 'Chills with shaking that don\'t settle can signal a higher fever — check your temperature.',
            ],
            'pain' => [
                'with' => ['fatigue', 'sleep disruption', 'irritability', 'reduced activity'],
                'why' => 'Pain keeps the nervous system on alert and makes sleep harder. Poor sleep then lowers pain tolerance, so the two feed each other.',
                'note' => 'Pain that wakes you from sleep, or that keeps getting worse, should be evaluated.',
            ],
            'skin' => [
                'with' => ['stress', 'digestive changes', 'itching', 'redness'],
                'why' => 'Breakouts and rashes are often triggered or worsened by stress (cortisol ramps up oil production) and can overlap with gut changes through shared immune pathways.',
                'note' => 'A rash that spreads quickly, blisters, or comes with fever needs prompt attention.',
            ],
            'redness' => [
                'with' => ['swelling', 'itching', 'warmth'],
                'why' => 'Redness is usually inflammation — blood vessels dilating in the area. It commonly pairs with swelling (fluid buildup) and a feeling of warmth.',
                'note' => 'Redness spreading with streaks, or coming with fever, may need a doctor\'s look.',
            ],
            'swelling' => [
                'with' => ['redness', 'pain', 'stiffness'],
                'why' => 'Swelling is fluid or tissue buildup, usually from inflammation or injury. It tends to come with redness and pain because the same process drives all three.',
                'note' => 'Swelling that is sudden, one-sided, or painful to touch is worth checking.',
            ],
            'respiratory' => [
                'with' => ['fever', 'fatigue', 'coughing', 'chest tightness'],
                'why' => 'Breathing issues often come with fever when an infection is involved. The extra effort of breathing and coughing wears you out, which is why fatigue shows up too.',
                'note' => 'Shortness of breath at rest or with minimal effort should be checked promptly.',
            ],
            'breathing' => [
                'with' => ['chest tightness', 'coughing', 'wheezing', 'fatigue'],
                'why' => 'Difficulty breathing frequently rides with airway or chest complaints, and the strain of working to breathe is exhausting.',
                'note' => 'Sudden or severe difficulty breathing is an emergency — do not wait it out.',
            ],
            'digestive' => [
                'with' => ['nausea', 'loss of appetite', 'stress', 'fatigue'],
                'why' => 'The gut is highly sensitive to stress (the gut–brain axis), so worry can cause nausea, cramping, or appetite changes. Digestive trouble also commonly brings fatigue.',
                'note' => 'Blood, severe pain, or unexplained weight loss warrants a professional visit.',
            ],
            'nausea' => [
                'with' => ['dizziness', 'loss of appetite', 'stomach cramping'],
                'why' => 'Nausea and dizziness often travel together — both can come from dehydration, low blood pressure, inner-ear signals, or the same stomach bug.',
                'note' => 'Nausea with severe dizziness or inability to keep fluids down is worth a check.',
            ],
            'dizziness' => [
                'with' => ['nausea', 'fatigue', 'lightheadedness'],
                'why' => 'Dizziness commonly pairs with nausea (they share balance and blood-pressure pathways) and fatigue, since feeling unsteady is draining.',
                'note' => 'Dizziness with fainting, chest pain, or a severe headache needs urgent attention.',
            ],
            'fatigue' => [
                'with' => ['sleep problems', 'low mood', 'headaches', 'digestive changes'],
                'why' => 'Fatigue rarely travels alone — it often pairs with poor sleep, low mood, and tension headaches, since being run down affects several systems at once.',
                'note' => 'Fatigue that persists despite good sleep, for more than 2 weeks, is worth a blood test.',
            ],
            'sweating' => [
                'with' => ['fever', 'chills', 'fatigue'],
                'why' => 'Sweating is the body\'s cooling system — it shows up as a fever breaks or when the body is working hard. Night sweats in particular often pair with fatigue.',
                'note' => 'Heavy night sweats with weight loss or persistent fever should be mentioned to a doctor.',
            ],
            'loss-of-appetite' => [
                'with' => ['nausea', 'digestive issues', 'fatigue', 'low mood'],
                'why' => 'Appetite drops when the body is fighting something or when mood is low — both nudge the hunger signals down. It also feeds fatigue when you\'re not getting enough fuel.',
                'note' => 'Losing weight without trying, or no appetite for more than a week, is worth a look.',
            ],
            'discharge' => [
                'with' => ['redness', 'swelling', 'pain'],
                'why' => 'Discharge is usually a sign of inflammation or infection in the area, so it commonly comes with redness, swelling, and local discomfort.',
                'note' => 'Discharge with strong odor, color change, or spreading pain should be evaluated.',
            ],
            'other' => [
                'with' => ['fatigue', 'sleep changes', 'stress'],
                'why' => 'Symptoms that don\'t fit a neat category still tend to pull in the same companions — being unwell uses energy, disrupts sleep, and raises stress.',
                'note' => 'Write down when it happens and what makes it better or worse — patterns help any professional you see.',
            ],
            'unsure' => [
                'with' => ['fatigue', 'sleep changes', 'stress', 'low mood'],
                'why' => 'When something feels off but doesn\'t have a clear label, it often turns out to be several things at once — stress, poor sleep, and low mood all blur together.',
                'note' => 'Keep a simple log for a week: when it happens, what you ate, how you slept. It\'s genuinely useful.',
            ],
        ],
        'mental' => [
            'anxiety' => [
                'with' => ['sleep problems', 'racing thoughts', 'physical tension', 'irritability'],
                'why' => 'Anxiety keeps the nervous system in a low-level alert state, which makes it hard to switch off at night. Racing thoughts at bedtime then feed back into more anxiety — a classic loop.',
                'note' => 'If worry stops you from doing things you used to enjoy, that\'s worth talking through with someone.',
            ],
            'depression' => [
                'with' => ['fatigue', 'low motivation', 'sleep changes', 'appetite changes', 'withdrawal'],
                'why' => 'Depression commonly saps energy and motivation, which leads to pulling back from people and activities. Sleep and appetite shifts are also typical, since depression affects the systems that regulate both.',
                'note' => 'If low mood has lasted more than 2 weeks, a professional can help you sort through it.',
            ],
            'stress' => [
                'with' => ['headaches', 'digestive issues', 'sleep problems', 'irritability', 'fatigue'],
                'why' => 'Chronic stress raises cortisol, which can trigger tension headaches, stomach trouble, and restless sleep. The physical toll then leaves you exhausted and quicker to snap.',
                'note' => 'Stress symptoms that persist after the stressful period ends may need a closer look.',
            ],
            'ocd' => [
                'with' => ['anxiety', 'rumination', 'guilt', 'checking behaviors'],
                'why' => 'OCD-like patterns are driven by anxiety — intrusive thoughts create discomfort, and the urge to check or "fix" things brings temporary relief, which reinforces the loop.',
                'note' => 'If checking or mental rituals take up a large part of your day, ERP-style therapy is the best-supported approach.',
            ],
            'social' => [
                'with' => ['anxiety', 'avoidance', 'self-criticism', 'physical tension'],
                'why' => 'Social discomfort usually rides on anxiety about judgment. Avoiding situations brings short-term relief but lets the fear grow over time.',
                'note' => 'Avoiding more and more situations is a sign it\'s worth building up exposure gradually — ideally with support.',
            ],
            'sleep' => [
                'with' => ['anxiety', 'fatigue', 'low mood', 'difficulty concentrating'],
                'why' => 'Poor sleep amplifies emotional reactivity and makes it harder to regulate worry. Daytime fatigue and brain fog then make everything feel heavier.',
                'note' => 'Persistent sleep trouble (more than a few weeks) is a common, treatable issue — a doctor or CBT-I specialist can help.',
            ],
            'trauma' => [
                'with' => ['anxiety', 'flashbacks', 'avoidance', 'sleep problems', 'hypervigilance'],
                'why' => 'After distressing experiences, the nervous system can stay on high alert — showing up as anxiety, poor sleep, and avoidance of reminders.',
                'note' => 'Trauma-informed therapy works at your own pace; you stay in control of what you share.',
            ],
            'unsure' => [
                'with' => ['stress', 'sleep changes', 'physical symptoms', 'low mood'],
                'why' => 'When something feels off but doesn\'t fit a label, it often turns out to be a mix — stress plus poor sleep plus physical strain can all blur together.',
                'note' => 'Writing down what you notice day to day can help a professional understand the full picture.',
            ],
        ],
        default => [],
    };

    // Build rows: reported symptoms first (categories/concerns), then specific present symptoms.
    $rows = [];
    foreach ($symptoms as $symptom) {
        if (isset($correlations[$symptom])) {
            $rows[$symptom] = $correlations[$symptom];
        }
    }
    foreach ($presentSymptoms as $symptom) {
        if (isset($correlations[$symptom]) && !isset($rows[$symptom])) {
            $rows[$symptom] = $correlations[$symptom];
        }
    }
@endphp

@if(count($rows) > 0)
<div class="animate-fade-in" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        {{-- Header --}}
        <div class="p-5 border-b border-warm-100 bg-{{ $themeClass }}-50/30">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-8 h-8 rounded-lg bg-{{ $themeClass }}-100 border border-{{ $themeClass }}-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-{{ $themeClass }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h2 class="font-display font-bold text-lg text-warm-800">How your symptoms compare</h2>
            </div>
            <p class="text-xs text-warm-500 leading-relaxed">This table shows which symptoms people <strong>commonly report together</strong> and why they tend to overlap. It's a <strong>comparison of patterns only — it is not a diagnosis</strong> and doesn't mean you have any condition.</p>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-warm-400 border-b border-warm-100 bg-warm-50/50">
                        <th class="px-5 py-3 font-semibold">Your symptom</th>
                        <th class="px-5 py-3 font-semibold">Commonly appears together with</th>
                        <th class="px-5 py-3 font-semibold">Why they tend to overlap</th>
                        <th class="px-5 py-3 font-semibold">Worth noting</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-100">
                    @foreach($rows as $key => $row)
                    <tr class="hover:bg-warm-50/60 transition-colors align-top">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-{{ $themeClass }}-50 border border-{{ $themeClass }}-200 font-medium text-{{ $themeClass }}-700">
                                {{ $labels[$key] ?? ucfirst(str_replace('-', ' ', $key)) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1.5 max-w-[16rem]">
                                @foreach($row['with'] as $w)
                                <span class="px-2 py-0.5 rounded-md bg-warm-100 text-xs text-warm-600">{{ $w }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-4 text-warm-600 leading-relaxed min-w-[18rem]">{{ $row['why'] }}</td>
                        <td class="px-5 py-4 text-warm-500 leading-relaxed min-w-[14rem]">
                            <div class="flex gap-1.5">
                                <svg class="w-3.5 h-3.5 text-warning shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $row['note'] }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer disclaimer --}}
        <div class="p-4 bg-warm-50/70 border-t border-warm-100">
            <p class="text-xs text-warm-400 leading-relaxed">Correlation is not causation. Symptoms overlapping with each other is common and usually harmless — many combinations are just normal variation or stress showing up physically. If anything here concerns you, the most useful next step is to mention it to a professional.</p>
        </div>
    </div>
</div>
@endif