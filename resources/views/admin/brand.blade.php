@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Brand Settings</h1>
        <p class="text-sm text-warm-500">Customize your brand appearance.</p>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/30 text-success rounded-xl p-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-warm-200 p-6 space-y-6">
        <div>
            <label class="block text-sm font-semibold text-warm-700 mb-3">Current Logo</label>
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-400 via-white to-sky-400 flex items-center justify-center shadow-sm border border-warm-200">
                <span class="text-warm-800 font-bold text-3xl">T</span>
            </div>
            <p class="text-xs text-warm-400 mt-2">Logo upload coming soon.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-warm-700 mb-3">Color Scheme Preview</label>
            <div class="flex gap-3">
                <div class="w-12 h-12 rounded-xl bg-physical-300 border border-physical-200" title="Physical"></div>
                <div class="w-12 h-12 rounded-xl bg-mental-300 border border-mental-200" title="Mental"></div>
                <div class="w-12 h-12 rounded-xl bg-other-300 border border-other-200" title="Other"></div>
            </div>
            <p class="text-xs text-warm-400 mt-2">Custom color editing coming soon.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-warm-700 mb-3">Social Links</label>
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-warm-500">Instagram URL</label>
                    <input type="url" placeholder="https://instagram.com/..." class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
                <div>
                    <label class="text-xs text-warm-500">Twitter / X URL</label>
                    <input type="url" placeholder="https://x.com/..." class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
                <div>
                    <label class="text-xs text-warm-500">TikTok URL</label>
                    <input type="url" placeholder="https://tiktok.com/..." class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
                <div>
                    <label class="text-xs text-warm-500">GitHub URL</label>
                    <input type="url" placeholder="https://github.com/..." class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
                </div>
            </div>
            <p class="text-xs text-warm-400 mt-2">Social link saving coming soon.</p>
        </div>
    </div>
</div>
@endsection
