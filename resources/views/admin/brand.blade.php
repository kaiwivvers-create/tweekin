@extends('layouts.app')
@php $pageTitle = "Admin Panel" @endphp

@section('content')
<div class="space-y-8 max-w-4xl" x-data="brandSettings()" x-init="init()">

    <div>
        <h1 class="font-display font-bold text-2xl text-warm-800 mb-1">Brand Settings</h1>
        <p class="text-sm text-warm-500">Customize your brand name, colors, logo, and social links. Changes apply across the entire app.</p>
    </div>

    @if(session('success'))
        <div class="bg-success/20 border border-success/30 text-success rounded-xl p-4 text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- App Name --}}
    <form method="POST" action="{{ route('admin.brand.update') }}" class="bg-white rounded-2xl border border-warm-200 p-6 space-y-4">
        @csrf
        <h2 class="font-display font-bold text-lg text-warm-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-physical-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            General
        </h2>
        <p class="text-sm text-warm-500">Basic app configuration — name, disclaimer, and branding.</p>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-warm-600 mb-1.5">App Name</label>
                <div class="flex items-center gap-4">
                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name', 'Tweek') }}"
                           class="flex-1 px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-physical-400 focus:ring-0 transition-colors text-sm font-medium"
                           placeholder="App name">
                    <div class="text-sm text-warm-400">Preview:</div>
                    <div class="px-4 py-2 rounded-xl bg-warm-100 font-display font-bold text-warm-800" x-text="appName || 'Tweek'"></div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-warm-600 mb-1.5">Disclaimer Text</label>
                <textarea name="disclaimer_text" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-physical-400 focus:ring-0 transition-colors text-sm resize-none"
                          placeholder="This is not a diagnostic tool...">{{ $settings['disclaimer_text'] ?? 'This is not a diagnostic tool. Always consult a qualified healthcare professional for medical advice.' }}</textarea>
                <p class="text-xs text-warm-400 mt-1">Shown at the top of every page in the yellow bar.</p>
            </div>
        </div>
        <input type="hidden" name="primary_color" :value="colors.primary">
        <input type="hidden" name="secondary_color" :value="colors.secondary">
        <input type="hidden" name="accent_color" :value="colors.accent">
        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-physical-300 hover:bg-physical-400 text-physical-800 font-semibold text-sm transition-colors">
                Save General Settings
            </button>
        </div>
    </form>

    {{-- Logo Upload --}}
    <div class="bg-white rounded-2xl border border-warm-200 p-6 space-y-4">
        <h2 class="font-display font-bold text-lg text-warm-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-mental-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Logo
        </h2>
        <p class="text-sm text-warm-500">Upload your logo. It will be cropped to a square. Recommended size: 512x512px or larger.</p>

        <div class="flex items-start gap-6">
            {{-- Current logo preview --}}
            <div class="shrink-0">
                <div id="logo-preview" class="w-24 h-24 rounded-2xl border-2 border-dashed border-warm-300 flex items-center justify-center overflow-hidden bg-warm-50"
                     style="{{ ($settings['logo_path'] ?? '') ? 'border-style: solid;' : '' }}">
                    @if($settings['logo_path'] ?? '')
                        <img src="{{ Storage::disk('public')->url($settings['logo_path']) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <div class="text-center">
                            <svg class="w-8 h-8 text-warm-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] text-warm-400 mt-0.5 block">No logo</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Upload area --}}
            <div class="flex-1 space-y-3">
                <div id="drop-zone"
                     class="border-2 border-dashed border-warm-300 rounded-xl p-6 text-center hover:border-mental-400 hover:bg-mental-50/30 transition-all cursor-pointer"
                     ondrop="event.preventDefault(); handleLogoDrop(event)"
                     ondragover="event.preventDefault(); this.classList.add('border-mental-400', 'bg-mental-50/30')"
                     ondragleave="this.classList.remove('border-mental-400', 'bg-mental-50/30')"
                     onclick="document.getElementById('logo-input').click()">
                    <input type="file" id="logo-input" accept="image/png,jpg,jpeg,svg,webp" class="hidden" onchange="handleLogoSelect(event)">
                    <svg class="w-8 h-8 text-warm-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-warm-500">Drop an image here or <span class="text-mental-500 font-medium">browse</span></p>
                    <p class="text-xs text-warm-400 mt-1">PNG, JPG, SVG, WebP — max 2MB</p>
                </div>

                @if($settings['logo_path'] ?? '')
                    <button type="button" onclick="deleteLogo()" class="text-xs text-red-400 hover:text-red-600 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Remove logo
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Color Scheme --}}
    <div class="bg-white rounded-2xl border border-warm-200 p-6 space-y-6">
        <h2 class="font-display font-bold text-lg text-warm-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-other-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            Color Scheme
        </h2>
        <p class="text-sm text-warm-500">Choose a preset or pick custom colors. These colors affect the entire app's theme.</p>

        {{-- Preset themes --}}
        <div>
            <label class="block text-xs font-semibold text-warm-600 uppercase tracking-wider mb-3">Preset Themes</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <template x-for="(preset, index) in presets" :key="index">
                    <button type="button" @click="applyPreset(preset)"
                            class="rounded-xl border-2 p-3 text-left transition-all hover:scale-[1.02]"
                            :class="JSON.stringify(colors) === JSON.stringify(preset.colors) ? 'border-mental-400 bg-mental-50 ring-2 ring-mental-200' : 'border-warm-200 hover:border-warm-300'">
                        <div class="flex gap-1.5 mb-2">
                            <div class="w-5 h-5 rounded-full" :style="'background:' + preset.colors.primary"></div>
                            <div class="w-5 h-5 rounded-full" :style="'background:' + preset.colors.secondary"></div>
                            <div class="w-5 h-5 rounded-full" :style="'background:' + preset.colors.accent"></div>
                        </div>
                        <span class="text-xs font-medium text-warm-700" x-text="preset.name"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Custom color pickers --}}
        <div>
            <label class="block text-xs font-semibold text-warm-600 uppercase tracking-wider mb-3">Custom Colors</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Physical / Primary --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-warm-700">Physical (Primary)</label>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="color" x-model="colors.primary" class="w-10 h-10 rounded-lg border-2 border-warm-200 cursor-pointer p-0">
                        </div>
                        <input type="text" x-model="colors.primary" class="flex-1 px-3 py-2 rounded-lg border-2 border-warm-200 bg-white text-warm-800 text-sm font-mono focus:border-physical-400 focus:ring-0">
                    </div>
                    <div class="flex gap-1 mt-1">
                        <div class="h-6 flex-1 rounded" :style="'background:' + colors.primary"></div>
                    </div>
                </div>

                {{-- Mental / Secondary --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-warm-700">Mental (Secondary)</label>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="color" x-model="colors.secondary" class="w-10 h-10 rounded-lg border-2 border-warm-200 cursor-pointer p-0">
                        </div>
                        <input type="text" x-model="colors.secondary" class="flex-1 px-3 py-2 rounded-lg border-2 border-warm-200 bg-white text-warm-800 text-sm font-mono focus:border-mental-400 focus:ring-0">
                    </div>
                    <div class="flex gap-1 mt-1">
                        <div class="h-6 flex-1 rounded" :style="'background:' + colors.secondary"></div>
                    </div>
                </div>

                {{-- Accent --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-warm-700">Accent</label>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="color" x-model="colors.accent" class="w-10 h-10 rounded-lg border-2 border-warm-200 cursor-pointer p-0">
                        </div>
                        <input type="text" x-model="colors.accent" class="flex-1 px-3 py-2 rounded-lg border-2 border-warm-200 bg-white text-warm-800 text-sm font-mono focus:border-other-400 focus:ring-0">
                    </div>
                    <div class="flex gap-1 mt-1">
                        <div class="h-6 flex-1 rounded" :style="'background:' + colors.accent"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live preview --}}
        <div>
            <label class="block text-xs font-semibold text-warm-600 uppercase tracking-wider mb-3">Live Preview</label>
            <div class="rounded-xl border border-warm-200 p-4 bg-warm-50">
                <div class="flex gap-3 mb-3">
                    <div class="px-4 py-2 rounded-xl text-sm font-semibold" :style="'background:' + colors.primary + '33; color:' + colors.primary">Physical</div>
                    <div class="px-4 py-2 rounded-xl text-sm font-semibold" :style="'background:' + colors.secondary + '33; color:' + colors.secondary">Mental</div>
                    <div class="px-4 py-2 rounded-xl text-sm font-semibold" :style="'background:' + colors.accent + '33; color:' + colors.accent">Other</div>
                </div>
                <div class="flex gap-2">
                    <div class="flex-1 h-8 rounded-lg" :style="'background:' + colors.primary"></div>
                    <div class="flex-1 h-8 rounded-lg" :style="'background:' + colors.secondary"></div>
                    <div class="flex-1 h-8 rounded-lg" :style="'background:' + colors.accent"></div>
                </div>
                <div class="mt-3 flex gap-2">
                    <div class="px-3 py-1.5 rounded-full text-xs font-medium" :style="'background:' + colors.primary + '; color: white'">Button</div>
                    <div class="px-3 py-1.5 rounded-full text-xs font-medium border-2" :style="'border-color:' + colors.secondary + '; color:' + colors.secondary">Outline</div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="button" @click="saveColors()" class="px-5 py-2 rounded-xl bg-mental-300 hover:bg-mental-400 text-mental-800 font-semibold text-sm transition-colors">
                Save Colors
            </button>
        </div>
    </div>

    {{-- Social Links --}}
    <form method="POST" action="{{ route('admin.brand.update') }}" class="bg-white rounded-2xl border border-warm-200 p-6 space-y-4">
        @csrf
        <input type="hidden" name="app_name" value="{{ $settings['app_name'] ?? config('app.name', 'Tweek') }}">
        <input type="hidden" name="primary_color" :value="colors.primary">
        <input type="hidden" name="secondary_color" :value="colors.secondary">
        <input type="hidden" name="accent_color" :value="colors.accent">

        <h2 class="font-display font-bold text-lg text-warm-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-physical-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            Social Links
        </h2>
        <p class="text-sm text-warm-500">Links shown in the footer. Leave blank to hide.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-warm-500 flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    Instagram
                </label>
                <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}"
                       placeholder="https://instagram.com/..."
                       class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
            </div>
            <div>
                <label class="text-xs text-warm-500 flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    X / Twitter
                </label>
                <input type="url" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}"
                       placeholder="https://x.com/..."
                       class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
            </div>
            <div>
                <label class="text-xs text-warm-500 flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-.81-.07l.81.07z"/></svg>
                    TikTok
                </label>
                <input type="url" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}"
                       placeholder="https://tiktok.com/..."
                       class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
            </div>
            <div>
                <label class="text-xs text-warm-500 flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    GitHub
                </label>
                <input type="url" name="social_github" value="{{ $settings['social_github'] ?? '' }}"
                       placeholder="https://github.com/..."
                       class="w-full px-4 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-other-300 hover:bg-other-400 text-other-800 font-semibold text-sm transition-colors">
                Save Social Links
            </button>
        </div>
    </form>

    {{-- Hero Image --}}
    <div class="bg-white rounded-2xl border border-warm-200 p-6 space-y-4">
        <h2 class="font-display font-bold text-lg text-warm-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-other-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Hero Image
        </h2>
        <p class="text-sm text-warm-500">Upload a custom image for the landing page hero section. This replaces the default decorative illustration.</p>

        <div class="flex items-start gap-6">
            <div class="shrink-0">
                <div id="hero-preview" class="w-48 h-28 rounded-2xl border-2 border-dashed border-warm-300 flex items-center justify-center overflow-hidden bg-warm-50"
                     style="{{ ($settings['hero_image_path'] ?? '') ? 'border-style: solid;' : '' }}">
                    @if($settings['hero_image_path'] ?? '')
                        <img src="{{ Storage::disk('public')->url($settings['hero_image_path']) }}" alt="Hero" class="w-full h-full object-cover">
                    @else
                        <div class="text-center">
                            <svg class="w-8 h-8 text-warm-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] text-warm-400 mt-0.5 block">Using default illustration</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex-1 space-y-3">
                <div class="border-2 border-dashed border-warm-300 rounded-xl p-6 text-center hover:border-other-400 hover:bg-other-50/30 transition-all cursor-pointer"
                     ondrop="event.preventDefault(); handleHeroDrop(event)"
                     ondragover="event.preventDefault(); this.classList.add('border-other-400', 'bg-other-50/30')"
                     ondragleave="this.classList.remove('border-other-400', 'bg-other-50/30')"
                     onclick="document.getElementById('hero-input').click()">
                    <input type="file" id="hero-input" accept="image/png,jpg,jpeg,svg,webp" class="hidden" onchange="handleHeroSelect(event)">
                    <svg class="w-8 h-8 text-warm-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-warm-500">Drop an image or <span class="text-other-500 font-medium">browse</span></p>
                    <p class="text-xs text-warm-400 mt-1">PNG, JPG, SVG, WebP — max 4MB. Recommended: 1200x800px</p>
                </div>
                @if($settings['hero_image_path'] ?? '')
                    <button type="button" onclick="deleteHero()" class="text-xs text-red-400 hover:text-red-600 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Remove hero image
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- AI / API Key --}}
    <div class="bg-white rounded-2xl border border-warm-200 p-6 space-y-4">
        <h2 class="font-display font-bold text-lg text-warm-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-mental-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            AI Configuration
        </h2>
        <p class="text-sm text-warm-500">Connect Google Gemini to power the AI chat on results pages. Get a free API key at <a href="https://aistudio.google.com/" target="_blank" class="text-mental-500 underline underline-offset-2">aistudio.google.com</a>.</p>

        <form method="POST" action="{{ route('admin.brand.api-key') }}" class="space-y-3">
            @csrf
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <input type="password" name="google_api_key" value="{{ $settings['google_api_key'] ?? '' }}"
                           placeholder="Paste your Google AI API key here..."
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-warm-200 bg-white text-warm-800 focus:border-mental-400 focus:ring-0 transition-colors text-sm font-mono"
                           autocomplete="off">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-mental-300 hover:bg-mental-400 text-mental-800 font-semibold text-sm transition-colors shrink-0">
                    Save Key
                </button>
            </div>
            @if($settings['google_api_key'] ?? '')
                <div class="flex items-center gap-2 text-xs text-success">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    API key configured — AI chat is active
                </div>
            @else
                <div class="flex items-center gap-2 text-xs text-warm-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    No API key — AI chat falls back to pre-written responses
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Cropper.js Modal --}}
<div id="cropper-modal" class="fixed inset-0 z-[70] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="px-6 py-4 border-b border-warm-200 flex items-center justify-between">
            <h3 class="font-display font-bold text-warm-800">Crop Logo</h3>
            <button onclick="closeCropper()" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4">
            <img id="cropper-image" class="max-h-[400px] w-full object-contain" src="">
        </div>
        <div class="px-6 py-4 border-t border-warm-200 flex justify-end gap-3">
            <button onclick="closeCropper()" class="px-4 py-2 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">Cancel</button>
            <button onclick="confirmCrop()" class="px-4 py-2 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-semibold transition-colors">Crop & Upload</button>
        </div>
    </div>
</div><script>

function brandSettings() {
    return {
        appName: @json($settings['app_name'] ?? config('app.name', 'Tweek')),
        colors: {
            primary: @json($settings['primary_color'] ?? '#FFF9E8'),
            secondary: @json($settings['secondary_color'] ?? '#EBF4FF'),
            accent: @json($settings['accent_color'] ?? '#F3EEFF'),
        },
        presets: [
            { name: 'Soft Pastel', colors: { primary: '#FFF9E8', secondary: '#EBF4FF', accent: '#F3EEFF' } },
            { name: 'Warm Sunset', colors: { primary: '#FFF1E6', secondary: '#FFE4E6', accent: '#FEF3C7' } },
            { name: 'Ocean Breeze', colors: { primary: '#E0F2FE', secondary: '#DBEAFE', accent: '#E0E7FF' } },
            { name: 'Forest', colors: { primary: '#ECFDF5', secondary: '#D1FAE5', accent: '#F0FDF4' } },
            { name: 'Lavender Dream', colors: { primary: '#F5F3FF', secondary: '#EDE9FE', accent: '#FDF2F8' } },
            { name: 'Peachy', colors: { primary: '#FFF7ED', secondary: '#FFEDD5', accent: '#FEF2F2' } },
            { name: 'Arctic', colors: { primary: '#F0F9FF', secondary: '#E0F2FE', accent: '#F0FDFA' } },
            { name: 'Rose Gold', colors: { primary: '#FFF1F2', secondary: '#FFE4E6', accent: '#FDF2F8' } },
        ],
        applyPreset(preset) {
            this.colors = { ...preset.colors };
        },
        saveColors() {
            const form = new FormData();
            form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            form.append('app_name', this.appName || 'Tweek');
            form.append('primary_color', this.colors.primary);
            form.append('secondary_color', this.colors.secondary);
            form.append('accent_color', this.colors.accent);
            fetch('{{ route("admin.brand.update") }}', {
                method: 'POST',
                body: form,
            }).then(r => {
                if (r.ok) window.location.reload();
            });
        },
        init() {
            // Make appName reactive to the input
            const nameInput = document.querySelector('input[name="app_name"]');
            if (nameInput) {
                nameInput.addEventListener('input', (e) => {
                    this.appName = e.target.value;
                });
            }
        }
    };
}

function handleLogoSelect(event) {
    const file = event.target.files[0];
    if (file) openCropper(file);
}

function handleLogoDrop(event) {
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) openCropper(file);
    document.getElementById('drop-zone').classList.remove('border-mental-400', 'bg-mental-50/30');
}

function openCropper(file) {
    pendingFile = file;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('cropper-image');
        img.src = e.target.result;
        document.getElementById('cropper-modal').classList.remove('hidden');

        if (cropper) cropper.destroy();
        cropper = new Cropper(img, {
            aspectRatio: 1,
            viewMode: 1,
            minCropBoxWidth: 128,
            minCropBoxHeight: 128,
            background: false,
            responsive: true,
            checkOrientation: true,
        });
    };
    reader.readAsDataURL(file);
}

function deleteLogo() {
    if (!confirm('Remove the current logo?')) return;
    fetch('{{ route("admin.brand.logo.delete") }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
    });
}

// Hero image functions
let heroCropper = null;
let heroFile = null;

function handleHeroSelect(event) {
    const file = event.target.files[0];
    if (file) openHeroCropper(file);
}

function handleHeroDrop(event) {
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) openHeroCropper(file);
    event.currentTarget.classList.remove('border-other-400', 'bg-other-50/30');
}

function openHeroCropper(file) {
    heroFile = file;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('cropper-image');
        img.src = e.target.result;
        document.getElementById('cropper-modal').classList.remove('hidden');
        document.querySelector('#cropper-modal h3').textContent = 'Crop Hero Image';

        if (heroCropper) heroCropper.destroy();
        heroCropper = new Cropper(img, {
            aspectRatio: 3/2,
            viewMode: 1,
            minCropBoxWidth: 200,
            minCropBoxHeight: 133,
            background: false,
            responsive: true,
            checkOrientation: true,
        });
    };
    reader.readAsDataURL(file);
}

function confirmCrop() {
    // Determine if it's a logo or hero crop
    if (heroCropper && heroFile) {
        heroCropper.getCroppedCanvas({ width: 1200, height: 800, imageSmoothingQuality: 'high' }).toBlob(function(blob) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('hero_image', blob, 'hero.png');

            fetch('{{ route("admin.brand.hero") }}', {
                method: 'POST',
                body: formData,
            })
            .then(r => {
                console.log('Hero response status:', r.status);
                return r.text();
            })
            .then(text => {
                console.log('Hero response body:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Upload failed: ' + JSON.stringify(data));
                    }
                } catch(e) {
                    alert('Server error: ' + text.substring(0, 500));
                }
            })
            .catch(err => {
                console.error('Hero upload error:', err);
                alert('Upload error: ' + err.message);
            });
        }, 'image/png', 0.95);
    } else if (cropper && pendingFile) {
        // Original logo crop
        cropper.getCroppedCanvas({ width: 512, height: 512, imageSmoothingQuality: 'high' }).toBlob(function(blob) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('logo', blob, 'logo.png');

            fetch('{{ route("admin.brand.logo") }}', {
                method: 'POST',
                body: formData,
            })
            .then(r => {
                console.log('Logo response status:', r.status);
                return r.text();
            })
            .then(text => {
                console.log('Logo response body:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Upload failed: ' + JSON.stringify(data));
                    }
                } catch(e) {
                    alert('Server error: ' + text.substring(0, 500));
                }
            })
            .catch(err => {
                console.error('Logo upload error:', err);
                alert('Upload error: ' + err.message);
            });
        }, 'image/png', 0.95);
    }
}

function closeCropper() {
    document.getElementById('cropper-modal').classList.add('hidden');
    if (cropper) { cropper.destroy(); cropper = null; }
    if (heroCropper) { heroCropper.destroy(); heroCropper = null; }
    pendingFile = null;
    heroFile = null;
}

function deleteHero() {
    if (!confirm('Remove the hero image?')) return;
    fetch('{{ route("admin.brand.hero.delete") }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
    });
}
</script>

@push('styles')
<style>
    .cropper-view-box, .cropper-face { border-radius: 50%; }
    .cropper-view-box { outline: 1px solid rgba(255,255,255,0.8); }
    .cropper-line, .cropper-point { background-color: rgba(255,255,255,0.8); }
</style>
@endpush

@endsection
