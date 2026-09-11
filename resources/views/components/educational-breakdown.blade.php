@php
    $type = $type ?? 'general';
    $concerns = $concerns ?? [];
    $duration = $duration ?? null;
    $severity = $severity ?? null;
    $presentSymptoms = $presentSymptoms ?? [];
    $themeClass = match($type) {
        'physical' => 'physical',
        'mental' => 'mental',
        default => 'other',
    };
@endphp

<div class="space-y-6 animate-fade-in" style="animation-delay: 0.15s;">

    {{-- What this pattern often involves --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="p-5 border-b border-warm-100 bg-{{ $themeClass }}-50/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-{{ $themeClass }}-100 border border-{{ $themeClass }}-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-{{ $themeClass }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h2 class="font-display font-bold text-lg text-warm-800">What this pattern often involves</h2>
            </div>
        </div>
        <div class="p-5">

            @if($type === 'mental')
                {{-- Mental health patterns --}}
                @php
                    $hasAnxiety = in_array('anxiety', $concerns);
                    $hasDepression = in_array('depression', $concerns);
                    $hasOcd = in_array('ocd', $concerns);
                    $hasStress = in_array('stress', $concerns);
                    $hasSocial = in_array('social', $concerns);
                    $hasUnsure = in_array('unsure', $concerns);
                @endphp

                @if($hasAnxiety || $hasOcd || $hasSocial)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">The Anxiety-Overthinking Loop</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">When anxiety, intrusive thoughts, or social worry happen together, they often feed into a cycle: high stress spikes cortisol and adrenaline, keeping your nervous system on high alert. That makes it nearly impossible to feel calm. When you are chronically on edge, your brain's frontal lobe — which handles emotional regulation and filtering thoughts — gets tired. As a result, intrusive thoughts or obsessive worries feel much louder, stickier, and harder to shake.</p>
                </div>
                @endif

                @if($hasDepression)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">The Depression-Withdrawal Cycle</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Depression often creates a feedback loop: low energy leads to withdrawal, which leads to isolation, which deepens the low mood. The brain interprets reduced social contact and activity as confirmation that something is wrong, which makes motivation drop even further. This is not laziness — it is your nervous system conserving energy because it perceives a threat. Breaking this cycle usually requires small, deliberate steps rather than waiting to "feel like it."</p>
                </div>
                @endif

                @if($hasStress)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">The Stress-Burnout Connection</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Chronic stress without recovery leads to burnout — a state where your body and brain essentially shut down protective mechanisms. You might feel detached, irritable, unable to concentrate, or like nothing matters. This is not a personality flaw. It is your nervous system saying "enough." Burnout recovery requires boundaries, rest, and often a reevaluation of what you are carrying.</p>
                </div>
                @endif

                @if($hasOcd)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Understanding OCD Patterns</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">When life or internal feelings feel chaotic, the brain often tries to grab onto control by latching onto specific worries, routines, or mental checks. This is common in OCD patterns — the urge to "solve" an intrusive thought actually feeds the loop. The more you try to neutralize the thought, the stronger it gets. Learning to recognize these as "false alarms" is a core part of managing them.</p>
                </div>
                @endif

                @if($hasSocial)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Social Discomfort Patterns</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Discomfort around people often stems from fear of judgment or rejection. Your brain learns to predict danger in social situations based on past experiences — even if those experiences were minor. The anticipation of discomfort is usually worse than the actual interaction. Small, low-stakes social exposures help retrain this response over time.</p>
                </div>
                @endif

                @if($hasUnsure && !$hasAnxiety && !$hasDepression && !$hasOcd && !$hasStress && !$hasSocial)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">When You're Not Sure What's Going On</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Not knowing what you are dealing with is completely valid. Mental health concerns do not always fit neatly into categories. Sometimes what feels like "one thing" is actually a combination of stress, sleep disruption, lifestyle factors, or even physical health issues manifesting emotionally. The important part is that you noticed something and decided to look into it. A professional can help you sort through the layers.</p>
                </div>
                @endif

            @elseif($type === 'physical')
                {{-- Physical health patterns --}}
                @php
                    $hasFever = in_array('fever', $presentSymptoms) || in_array('fever', $concerns);
                    $hasPain = in_array('pain', $presentSymptoms) || in_array('pain', $concerns);
                    $hasSkin = in_array('skin', $presentSymptoms) || in_array('skin', $concerns);
                    $hasRespiratory = in_array('respiratory', $presentSymptoms) || in_array('respiratory', $concerns);
                    $hasDigestive = in_array('digestive', $presentSymptoms) || in_array('digestive', $concerns);
                    $hasFatigue = in_array('fatigue', $presentSymptoms) || in_array('fatigue', $concerns);
                    $hasUnsure = in_array('unsure', $concerns);
                @endphp

                @if($hasFever)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Fever and Temperature Changes</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">A fever is your body's immune system actively fighting something — usually an infection. Low-grade fevers (under 101°F / 38.3°C) often indicate a viral infection that your body is handling. Higher or persistent fevers may suggest a bacterial infection or inflammatory condition. Track when the fever peaks (morning vs. evening), whether it responds to medication, and if it is accompanied by other symptoms like chills, body aches, or sweating.</p>
                </div>
                @endif

                @if($hasPain)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Understanding Pain Patterns</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Pasteurella pain has many sources: muscular strain, nerve irritation, inflammation, or referred pain from another area. Key questions to narrow it down: Is it constant or intermittent? Does it worsen with movement, position, or time of day? Is it sharp (usually nerve or acute injury) or dull/aching (usually muscular or inflammatory)? Pain that wakes you from sleep, progressively worsens, or comes with numbness or weakness should be evaluated sooner rather than later.</p>
                </div>
                @endif

                @if($hasRespiratory)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Breathing and Respiratory Symptoms</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Respiratory symptoms can stem from infections (cold, flu, pneumonia), allergies, asthma, or even anxiety (which causes real physical breathing difficulty). Shortness of breath that occurs at rest, with minimal exertion, or that comes on suddenly is more concerning than gradual breathlessness during activity. Wheezing, chest tightness, and a cough that produces colored phlegm all help narrow down the cause.</p>
                </div>
                @endif

                @if($hasDigestive)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Digestive and Gut Issues</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Digestive symptoms often have multiple overlapping causes: dietary triggers, stress (the gut-brain connection is very real), infections, or functional disorders like IBS. Key patterns to track: Does it happen after specific foods? Is it related to stress or meal timing? Does it include changes in bowel habits, bloating, or nausea? Chronic digestive issues that persist beyond 2-3 weeks or include blood, severe pain, or unexplained weight loss warrant professional evaluation.</p>
                </div>
                @endif

                @if($hasSkin)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Skin Changes and Breakouts</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Skin changes can indicate infections, allergic reactions, hormonal shifts, autoimmune conditions, or stress responses. Track whether the breakout is localized or widespread, whether it itches, burns, or is painless, and whether it appeared suddenly or gradually. Rashes that spread rapidly, blister, or come with fever need prompt attention. Gradual changes are often manageable but still worth documenting for a professional.</p>
                </div>
                @endif

                @if($hasFatigue)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Fatigue and Low Energy</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Persistent fatigue has a wide range of causes: sleep disorders, nutritional deficiencies (iron, vitamin D, B12), thyroid issues, depression, chronic infection, or simply inadequate recovery. The key distinction is whether you are tired (sleepy, relieved by rest) or fatigued (bone-deep exhaustion that rest does not fix). Fatigue that persists despite adequate sleep, lasts more than 2 weeks, or comes with other symptoms like weight changes or hair loss should be evaluated with blood work.</p>
                </div>
                @endif

                @if($hasUnsure && !$hasFever && !$hasPain && !$hasRespiratory && !$hasDigestive && !$hasSkin && !$hasFatigue)
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">When You're Not Sure What's Going On</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Not knowing what category your symptoms fall into is very common. Physical symptoms can be caused by infections, stress (which has real physical effects), nutritional issues, hormonal changes, or lifestyle factors. The important thing is that you noticed something and are paying attention. Start by documenting when symptoms occur, what makes them better or worse, and how long they last. This information is invaluable for any professional you consult.</p>
                </div>
                @endif
            @else
                <div class="mb-4">
                    <h3 class="font-semibold text-warm-800 text-sm mb-2">Understanding Your Concerns</h3>
                    <p class="text-sm text-warm-600 leading-relaxed">Your concerns are valid regardless of which category they fall into. Sometimes symptoms cross boundaries — physical issues can affect mental health and vice versa. The fact that you are taking the time to understand what you are experiencing is a positive step. A professional can help you sort through the layers and determine the best path forward.</p>
                </div>
            @endif

        </div>
    </div>

    {{-- Practical Strategies --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="p-5 border-b border-warm-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-success/15 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="font-display font-bold text-lg text-warm-800">Practical strategies</h2>
            </div>
        </div>
        <div class="p-5 space-y-4">

            @if($type === 'mental')
                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-mental-600">1</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Cognitive Behavioral Techniques (CBT / ERP Concepts)</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">If you are dealing with OCD-style loops or intrusive thoughts, a core strategy is learning to recognize a "false alarm." Trying to argue with or figure out an intrusive thought actually feeds the loop. Techniques like <em>response prevention</em> teach your brain that you can have an uncomfortable thought without having to mentally "solve" it.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-mental-600">2</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Brain Dumps Before Bed</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">If your sleep is ruined because your brain is racing, try a physical "brain dump" 1–2 hours before sleep. Write down every single thing you are stressed about or feel you need to remember. Tell your brain: "It's on paper, I don't have to hold onto it until morning."</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-mental-600">3</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Nervous System Regulation</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">When you have been dealing with months of stress, simple deep breathing sometimes is not enough. Try <strong>physiological sighs</strong>: two quick inhales through the nose, one long exhale through the mouth. This can manually downshift your nervous system. The <strong>5-4-3-2-1 grounding technique</strong> (name 5 things you see, 4 you touch, 3 you hear, 2 you smell, 1 you taste) can also interrupt spiraling thoughts.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-mental-600">4</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Daily Foundations</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">Consistent sleep schedule, 20 minutes of movement daily, limiting doomscrolling, and connecting with one person per day. These are not "cures" — they are the foundation your brain needs to regulate itself. Without them, everything else is harder.</p>
                    </div>
                </div>

            @elseif($type === 'physical')
                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-physical-600">1</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Track Your Symptoms</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">Keep a simple log: when symptoms start, what you were doing, what you ate, and how severe it was on a 1-10 scale. Patterns emerge over time that are hard to see day-to-day. This information is gold for any doctor visit.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-physical-600">2</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Rule Out the Basics</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">Many physical symptoms have simple explanations: dehydration, poor sleep, nutritional gaps, or stress. Before jumping to worst-case scenarios, make sure you are drinking enough water, sleeping 7-9 hours, and eating a balanced diet. These basics are often overlooked.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-physical-600">3</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Know When to Act</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">Some symptoms need prompt attention: fever above 103°F / 39.4°C, difficulty breathing, chest pain, sudden severe headache, unexplained bleeding, or symptoms that rapidly worsen. For everything else, tracking for 1-2 weeks and then consulting a professional is usually appropriate.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-6 h-6 rounded-full bg-physical-100 border border-physical-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-[10px] font-bold text-physical-600">4</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-warm-800 text-sm mb-0.5">Reduce Stress-Related Physical Symptoms</h4>
                        <p class="text-sm text-warm-600 leading-relaxed">Stress causes very real physical symptoms: headaches, stomach issues, muscle tension, fatigue, and even skin breakouts. If your symptoms started during a stressful period, the stress itself might be the primary driver. Addressing the stress often resolves the physical symptoms.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- What to tell your doctor --}}
    <div class="bg-white rounded-2xl border border-warm-200 overflow-hidden">
        <div class="p-5 border-b border-warm-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-mental-100 border border-mental-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-mental-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <h2 class="font-display font-bold text-lg text-warm-800">What to tell a professional</h2>
            </div>
        </div>
        <div class="p-5">
            <p class="text-sm text-warm-600 leading-relaxed mb-4">If you decide to talk to a professional, here is a helpful way to frame what you have been experiencing:</p>

            <div class="bg-mental-50 border border-mental-200 rounded-xl p-4 mb-4">
                @if($type === 'mental')
                    <p class="text-sm text-mental-700 leading-relaxed italic">"I have been dealing with {{ $duration ? str_replace('-', ' ', $duration) : 'several weeks' }} of
                    @if(count($concerns) > 0)
                        {{ collect($concerns)->map(fn($c) => str_replace('-', ' ', $c))->implode(', ') }}
                    @else
                        mental health concerns
                    @endif
                    . It is impacting my daily functioning, and I would like to get a professional perspective on what might be going on and what my options are."</p>
                @else
                    <p class="text-sm text-mental-700 leading-relaxed italic">"I have been experiencing
                    @if(count($concerns) > 0)
                        {{ collect($concerns)->map(fn($c) => str_replace('-', ' ', $c))->implode(', ') }}
                    @else
                        physical symptoms
                    @endif
                    for {{ $duration ? str_replace('-', ' ', $duration) : 'a while' }}. I would like to get it checked out to understand what might be causing it."</p>
                @endif
            </div>

            <p class="text-sm text-warm-500 leading-relaxed"><strong>Also mention:</strong> when it started, what makes it better or worse, any changes in your daily life around that time, family history, and any medications or supplements you are taking.</p>
        </div>
    </div>

</div>
