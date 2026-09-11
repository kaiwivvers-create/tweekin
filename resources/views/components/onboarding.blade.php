{{-- Onboarding tour for first-time visitors --}}
@if(!Auth::check())
<div id="onboarding-overlay" class="hidden">
    {{-- Backdrop (only visible while the whole overlay is unhidden) --}}
    <div id="onboarding-backdrop" class="fixed inset-0 bg-warm-900/40 backdrop-blur-sm z-[70]"></div>

    {{-- Tooltip card --}}
    <div class="fixed inset-0 z-[71] flex items-center justify-center p-6">
        <div id="onboarding-card" class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 relative">
            {{-- Step indicator --}}
            <div id="onboarding-progress" class="flex items-center gap-1.5 mb-6"></div>

            {{-- Content --}}
            <div class="mb-8">
                <h3 id="onboarding-title" class="font-display font-bold text-xl text-warm-800 mb-2"></h3>
                <p id="onboarding-text" class="text-warm-500 leading-relaxed"></p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <button id="onboarding-skip" class="text-sm text-warm-400 hover:text-warm-600 transition-colors">
                    Skip tour
                </button>
                <button id="onboarding-next" class="px-5 py-2.5 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-semibold transition-colors">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('onboarding-overlay');
    if (!overlay) return;
    if (localStorage.getItem('tweek-onboarded')) return;

    var appName = '{{ str_replace("'", "\\'", config("app.name", "Tweek")) }}';

    var steps = [
        { title: appName, text: 'A preliminary symptom checker that helps you understand what you are experiencing. Not a diagnosis, just some clarity.' },
        { title: 'Pick a category', text: 'Choose Physical, Mental, or Not Sure. Each path is tailored to help you think through what is going on.' },
        { title: 'Answer some questions', text: 'We will ask about your symptoms, how long they have been going on, and how intense they feel. Take your time.' },
        { title: 'Get perspective', text: 'After the screening, you will get a summary of what you shared and guidance on whether it might be worth talking to a professional.' },
        { title: 'All set!', text: 'No account needed, no data sold. You can sign in later if you want to save your history. Ready to start?' }
    ];

    var step = 0;

    function render() {
        var current = steps[step];
        document.getElementById('onboarding-title').textContent = current.title;
        document.getElementById('onboarding-text').textContent = current.text;
        document.getElementById('onboarding-next').textContent = step < steps.length - 1 ? 'Next' : 'Lets go';

        var progress = document.getElementById('onboarding-progress');
        progress.innerHTML = '';
        steps.forEach(function(s, i) {
            var bar = document.createElement('div');
            bar.className = 'h-1 flex-1 rounded-full transition-colors duration-300 ' + (i <= step ? 'bg-mental-400' : 'bg-warm-200');
            progress.appendChild(bar);
        });
    }

    function dismiss() {
        overlay.classList.add('hidden');
        localStorage.setItem('tweek-onboarded', '1');
    }

    document.getElementById('onboarding-backdrop').addEventListener('click', dismiss);
    document.getElementById('onboarding-skip').addEventListener('click', dismiss);
    document.getElementById('onboarding-next').addEventListener('click', function() {
        if (step < steps.length - 1) {
            step++;
            render();
        } else {
            dismiss();
        }
    });

    overlay.classList.remove('hidden');
    render();
});
</script>
@endif