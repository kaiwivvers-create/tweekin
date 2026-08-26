<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle ?? config('app.name', 'Tweek') }} — Symptom Checker</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-warm-50 text-warm-800 font-sans">

    <div class="bg-warm-100 border-b border-warm-200 text-center py-2 px-4 text-xs text-warm-500">
        <span class="inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-mental-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            This is not a diagnostic tool. Always consult a qualified healthcare professional for medical advice.
        </span>
    </div>

    @include('partials.header')

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

    <script>
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
        window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    </script>
</body>
</html>
