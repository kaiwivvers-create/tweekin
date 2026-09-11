<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle ?? ($brand['name'] ?? config('app.name', 'Tweek')) }} — Symptom Checker</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-warm-50 text-warm-800 font-sans">
    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
    </script>

    @include('partials.header')

    @auth
        <div class="flex">
            @include('partials.sidebar')
            <main class="flex-1 lg:pl-64">
                <div x-data="{ show: localStorage.getItem('disclaimer-dismissed') !== '1' }" x-show="show" x-cloak
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-full opacity-0"
                     class="disclaimer-bar bg-warm-100 border-b border-warm-200 text-center py-1.5 px-4 text-[11px] text-warm-400 relative z-10">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-mental-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $brand['disclaimer'] ?? 'This is not a diagnostic tool. Always consult a qualified healthcare professional for medical advice.' }}
                    </span>
                    <button @click="show = false; localStorage.setItem('disclaimer-dismissed', '1')" class="absolute right-2 top-1/2 -translate-y-1/2 text-warm-400 hover:text-warm-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @yield('content')
            </main>
        </div>
    @else
        <div x-data="{ show: localStorage.getItem('disclaimer-dismissed') !== '1' }" x-show="show" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-full opacity-0"
             class="disclaimer-bar bg-warm-100 border-b border-warm-200 text-center py-1.5 px-4 text-[11px] text-warm-400 relative">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-3 h-3 text-mental-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $brand['disclaimer'] ?? 'This is not a diagnostic tool. Always consult a qualified healthcare professional for medical advice.' }}
            </span>
            <button @click="show = false; localStorage.setItem('disclaimer-dismissed', '1')" class="absolute right-2 top-1/2 -translate-y-1/2 text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <main class="flex-1">
            @yield('content')
        </main>
    @endauth

    @yield('footer')

    <div id="modal-container" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="modal-backdrop absolute inset-0" onclick="closeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div id="modal-content" class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[85vh] overflow-y-auto animate-scale-in"></div>
        </div>
    </div>

    @include('components.modals.disclaimer')
    @include('components.modals.privacy')
    @include('components.modals.terms')
    @include('components.modals.about')
    @auth
    @include('components.modals.profile')
    @endauth
    @include('components.modals.resources')
    @include('components.modals.neurodivergent')
    @include('components.onboarding')

    {{-- Shared Avatar Cropper Modal (must be real DOM, not template) --}}
    <div id="avatar-cropper-modal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-warm-800">Crop your photo</h3>
                <button onclick="closeAvatarCropper()" class="text-warm-400 hover:text-warm-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="rounded-xl overflow-hidden mb-4 bg-warm-100">
                <img id="avatar-cropper-image" class="block max-h-[350px] w-full" src="">
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="closeAvatarCropper()" class="px-4 py-2 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">Cancel</button>
                <button id="avatar-crop-btn" onclick="confirmAvatarCrop()" class="px-4 py-2 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-semibold transition-colors">Use this photo</button>
            </div>
        </div>
    </div>

    <script>
        // System-wide checkbox visual fix
        // peer-checked: only works on direct siblings, so inner checkmark elements need JS.
        document.addEventListener('DOMContentLoaded', function() {
            var themeColors = {
                physical: '#5b9a6b',
                mental: '#7b8fbe',
                warm: '#a08b7a'
            };

            function getTheme(cb) {
                var label = cb.closest('label');
                if (!label) return 'warm';
                var sibs = label.children;
                for (var i = 0; i < sibs.length; i++) {
                    if (sibs[i] !== cb && sibs[i].className) {
                        if (sibs[i].className.indexOf('peer-checked:border-physical-') !== -1) return 'physical';
                        if (sibs[i].className.indexOf('peer-checked:border-mental-') !== -1) return 'mental';
                    }
                }
                return 'warm';
            }

            function findCheckmarkBox(card) {
                // Find the SVG that contains the checkmark path (M5 13l4 4L19 7)
                var svgs = card.querySelectorAll('svg');
                for (var i = 0; i < svgs.length; i++) {
                    var path = svgs[i].querySelector('path[d*="5 13l4 4"]');
                    if (path) return svgs[i].parentElement; // the box div
                }
                return null;
            }

            function syncCheckmark(cb) {
                var label = cb.closest('label');
                if (!label) return;
                var card = null;
                var sibs = label.children;
                for (var i = 0; i < sibs.length; i++) {
                    if (sibs[i] !== cb && sibs[i].className && sibs[i].className.indexOf('peer-checked:') !== -1) {
                        card = sibs[i]; break;
                    }
                }
                if (!card) return;
                var box = findCheckmarkBox(card);
                if (!box) return;
                var svg = box.querySelector('svg');
                var theme = getTheme(cb);
                var color = themeColors[theme] || themeColors.warm;
                if (cb.checked) {
                    box.style.borderColor = color;
                    box.style.backgroundColor = color;
                    if (svg) { svg.style.transform = 'scale(1)'; svg.style.transition = 'transform 0.15s ease'; }
                } else {
                    box.style.borderColor = '';
                    box.style.backgroundColor = '';
                    if (svg) { svg.style.transform = 'scale(0)'; svg.style.transition = 'transform 0.15s ease'; }
                }
            }
            document.querySelectorAll('input[type="checkbox"].peer').forEach(function(cb) {
                cb.addEventListener('change', function() { syncCheckmark(cb); });
                if (cb.checked) syncCheckmark(cb);
            });
        });

        // Modal system
        function openModal(name) {
            const container = document.getElementById('modal-container');
            const content = document.getElementById('modal-content');
            const modal = document.getElementById('modal-' + name);
            if (modal) {
                content.innerHTML = modal.innerHTML;
                container.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
        function openModalWithData(name, data) {
            const container = document.getElementById('modal-container');
            const content = document.getElementById('modal-content');
            const modal = document.getElementById('modal-' + name);
            if (modal) {
                // Inject data as a script tag so the modal's init can read it
                content.innerHTML = modal.innerHTML;
                const initScript = document.createElement('script');
                initScript.textContent = 'try { window._modalData = ' + JSON.stringify(data) + '; } catch(e) { window._modalData = {}; }';
                content.appendChild(initScript);
                container.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
        function closeModal() {
            const container = document.getElementById('modal-container');
            container.classList.add('hidden');
            document.body.style.overflow = '';
        }
        window.addEventListener('open-modal', (e) => openModal(e.detail));
        window.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeModal(); closeAvatarCropper(); } });        // Neurodivergence picks — send to AI, show result in-modal
        function saveNeurodivergence() {
            const checkboxes = document.querySelectorAll('#modal-content input[name="neurodivergence[]"]:checked');
            const picks = Array.from(checkboxes).map(cb => cb.value);
            if (picks.length === 0) {
                const body = document.querySelector('#modal-content .space-y-4');
                if (body) {
                    body.innerHTML = '<div class="bg-danger/10 border border-danger/30 rounded-xl p-4 text-sm">' +
                        '<p class="font-medium text-danger">Pick at least one option that resonates with you.</p>' +
                        '<button type="button" onclick="location.reload();" class="mt-3 text-mental-500 underline hover:text-mental-600">Try again</button>' +
                        '</div>';
                }
                return;
            }
            const body = document.querySelector('#modal-content .space-y-4');
            const footer = document.querySelector('#modal-content .px-5.pb-5');
            const sendBtn = document.querySelector('#modal-content .bg-mental-400');

            // Build the AI message from what the user picked + the screening context
            const data = window._neurodivergenceModalData || {};
            const labelMap = {
                sensory: 'Sensory sensitivities',
                dopamine: 'Highs and lows around interest / motivation',
                burnout: 'Burnout from masking or overexerting',
                sleep: 'Sleep rhythm issues',
                stims: 'Repetitive movements or habits (stimming)',
                routines: 'Routines feel non-negotiable',
                notsure: 'Unsure / want to know more'
            };
            const pickedLabels = picks.map(k => labelMap[k] || k).join(', ');
            const msg = 'I picked the following and I would like to know what neurodevelopmental, neurological, or cognitive-difference-related things these could be related to, in plain language. What I picked: ' + pickedLabels + '. Here is the rest of what I shared in my screening: ' +
                (data.concerns ? 'Concerns: ' + data.concerns.join(', ') + '. ' : '') +
                (data.presentSymptoms && data.presentSymptoms.length ? 'Additional symptoms: ' + data.presentSymptoms.join(', ') + '. ' : '') +
                (data.duration ? 'Duration: ' + data.duration + '. ' : '') +
                (data.frequency ? 'Frequency: ' + data.frequency + '. ' : '') +
                (data.impact && data.impact.length ? 'Areas affected: ' + data.impact.join(', ') + '. ' : '') +
                (data.interference ? 'How much it gets in the way: ' + data.interference + '/5. ' : '') +
                (data.talkedTo ? 'Have spoken to: ' + data.talkedTo + '. ' : '') +
                (data.tried && data.tried.length ? 'Things I have tried: ' + data.tried.join(', ') + '. ' : '') +
                (data.whyThinking ? 'Why I think this: ' + data.whyThinking + '. ' : '') +
                'Please explain what these could be related to, what each thing typically involves, and what would be helpful to know or do next. Keep it clear and useful, not vague. This is not a crisis.';

            if (body) {
                body.innerHTML = '<div class="flex gap-3 animate-fade-in py-2">' +
                    '<div class="w-8 h-8 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center shrink-0">' +
                    '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>' +
                    '</div>' +
                    '<div class="bg-mental-50 border border-mental-200 rounded-2xl rounded-tl-md px-4 py-3">' +
                    '<div class="flex gap-1.5">' +
                    '<div class="w-2 h-2 rounded-full bg-mental-300 animate-bounce"></div>' +
                    '<div class="w-2 h-2 rounded-full bg-mental-300 animate-bounce" style="animation-delay:0.15s"></div>' +
                    '<div class="w-2 h-2 rounded-full bg-mental-300 animate-bounce" style="animation-delay:0.3s"></div>' +
                    '</div></div></div>';
            }
            if (sendBtn) { sendBtn.textContent = 'Thinking...'; sendBtn.disabled = true; }

            fetch('{{ route("api.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json'
                },
                body: JSON.stringify({
                    message: msg,
                    history: [],
                    context: Object.assign({}, data, {
                        type: data.type || (data.concerns ? 'mental' : 'physical'),
                        symptoms: data.concerns || [],
                        presentSymptoms: data.presentSymptoms || [],
                        neurodivergenceTraits: picks
                    })
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(json) {
                if (sendBtn) { sendBtn.textContent = 'Save my picks'; sendBtn.disabled = false; }
                if (json.response) {
                    if (body) {
                        body.innerHTML = '';
                        const card = document.createElement('div');
                        card.className = 'bg-mental-50 border border-mental-200 rounded-xl p-5 space-y-4 animate-fade-in';

                        const header = document.createElement('p');
                        header.className = 'font-medium text-mental-700 text-sm flex items-start gap-2';
                        header.innerHTML = '<svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg> Here is what this could relate to, based on what you shared:';
                        card.appendChild(header);

                        const contentDiv = document.createElement('div');
                        contentDiv.className = 'text-sm text-warm-700 leading-relaxed space-y-3';                        // Use shared markdown renderer from results-sections.js
                        renderAIMarkdown(contentDiv, json.response, 'mental');
                        card.appendChild(contentDiv);

                        const footer = document.createElement('p');
                        footer.className = 'text-xs text-warm-400 italic leading-relaxed border-t border-mental-100 pt-3';
                        footer.textContent = 'AI-generated information, not a diagnosis. A professional can give you a proper evaluation.';
                        card.appendChild(footer);

                        body.appendChild(card);
                        contentDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                } else {
                    if (body) {
                        body.innerHTML = '<div class="bg-danger/10 border border-danger/30 rounded-xl p-4 text-sm">' +
                            '<p class="font-medium text-danger">Could not get a response right now. Please try again in a moment.</p>' +
                            '<button type="button" onclick="location.reload();" class="mt-3 text-mental-500 underline hover:text-mental-600">Try again</button>' +
                            '</div>';
                    }
                }
            })
            .catch(function() {
                if (sendBtn) { sendBtn.textContent = 'Save my picks'; sendBtn.disabled = false; }
                if (body) {
                    body.innerHTML = '<div class="bg-danger/10 border border-danger/30 rounded-xl p-4 text-sm">' +
                        '<p class="font-medium text-danger">Something went wrong. Please try again.</p>' +
                        '<button type="button" onclick="location.reload();" class="mt-3 text-mental-500 underline hover:text-mental-600">Retry</button>' +
                        '</div>';
                }
            });
        }

        // Avatar cropper
        let avatarCropper = null;
        let pendingAvatarBlob = null;

        function handleAvatarSelect(event) {
            const file = event.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-cropper-image');
                img.src = e.target.result;
                document.getElementById('avatar-cropper-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                if (avatarCropper) avatarCropper.destroy();
                avatarCropper = new window.Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1,
                    minCropBoxSize: 80,
                    background: false,
                    autoCropArea: 1,
                });
            };
            reader.readAsDataURL(file);
        }

        function confirmAvatarCrop() {
            if (!avatarCropper) return;
            const btn = document.getElementById('avatar-crop-btn');
            btn.textContent = 'Uploading...';
            btn.disabled = true;

            avatarCropper.getCroppedCanvas({ width: 512, height: 512 }).toBlob(function(blob) {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('avatar', blob, 'avatar.png');

                fetch('{{ route("profile.avatar") }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(r => r.json())
                .then(data => {
                    btn.textContent = 'Use this photo';
                    btn.disabled = false;

                    if (data.success) {
                        // Update preview in profile modal
                        const avatarImg = document.getElementById('profile-avatar-img');
                        if (avatarImg) {
                            if (avatarImg.tagName === 'IMG') {
                                avatarImg.src = data.url;
                            } else {
                                const newImg = document.createElement('img');
                                newImg.id = 'profile-avatar-img';
                                newImg.src = data.url;
                                newImg.className = 'w-20 h-20 rounded-full object-cover border-3 border-mental-200 shadow-md';
                                newImg.alt = 'Avatar';
                                avatarImg.parentNode.replaceChild(newImg, avatarImg);
                            }
                        }
                        // Update header avatar
                        const headerAvatar = document.getElementById('header-avatar');
                        if (headerAvatar) {
                            if (headerAvatar.tagName === 'IMG') {
                                headerAvatar.src = data.url;
                            } else {
                                const newImg = document.createElement('img');
                                newImg.id = 'header-avatar';
                                newImg.src = data.url;
                                newImg.alt = 'Avatar';
                                newImg.className = 'w-8 h-8 rounded-full object-cover border-2 border-mental-200 shadow-sm group-hover:scale-105 transition-transform';
                                headerAvatar.parentNode.replaceChild(newImg, headerAvatar);
                            }
                        }
                        closeAvatarCropper();
                    } else {
                        alert('Upload failed. Please try again.');
                    }
                })
                .catch(() => {
                    btn.textContent = 'Use this photo';
                    btn.disabled = false;
                    alert('Upload failed. Please try again.');
                });
            }, 'image/png');
        }

        function closeAvatarCropper() {
            document.getElementById('avatar-cropper-modal').classList.add('hidden');
            if (avatarCropper) { avatarCropper.destroy(); avatarCropper = null; }
            pendingAvatarBlob = null;
        }
    </script>
</body>
</html>
