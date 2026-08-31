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

    @auth
        <div class="flex">
            @include('partials.sidebar')
            <main class="flex-1 lg:pl-64">
                @yield('content')
            </main>
        </div>
    @else
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
    @include('components.modals.profile')
    @include('components.modals.resources')

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
        function closeModal() {
            const container = document.getElementById('modal-container');
            container.classList.add('hidden');
            document.body.style.overflow = '';
        }
        window.addEventListener('open-modal', (e) => openModal(e.detail));
        window.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeModal(); closeAvatarCropper(); } });

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
