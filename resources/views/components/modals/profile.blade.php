<template id="modal-profile">
    <div class="p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-display font-bold text-lg text-warm-800">Profile Settings</h3>
            <button onclick="closeModal()" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Avatar upload --}}
        <div class="mb-6 text-center">
            <div class="relative inline-block mb-3">
                @if(Auth::user()->avatar)
                    <img id="profile-avatar-img" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                         class="w-20 h-20 rounded-full object-cover border-3 border-mental-200 shadow-md">
                @else
                    <div id="profile-avatar-img" class="w-20 h-20 rounded-full bg-gradient-to-br from-mental-300 to-mental-500 flex items-center justify-center border-3 border-mental-200 shadow-md">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                @endif
                <button type="button" onclick="document.getElementById('avatar-upload-input').click()"
                        class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-mental-400 hover:bg-mental-500 text-white shadow-md flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
                <input type="file" id="avatar-upload-input" accept="image/*" class="hidden" onchange="handleAvatarSelect(event)">
            </div>
            <p class="text-xs text-warm-400">Click the camera icon to change your photo</p>
        </div>

        {{-- Profile info --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-warm-700 mb-3">Profile information</h4>
            <p class="text-xs text-warm-400 mb-3">Update your name and email address.</p>

            <form id="profile-form" method="post" action="{{ route('profile.update') }}" class="space-y-3">
                @csrf
                @method('patch')

                <div>
                    <label class="block text-xs font-semibold text-warm-600 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required autofocus
                           class="w-full px-3 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                    @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-warm-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                           class="w-full px-3 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                    @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-semibold transition-colors">Save</button>
                    @if(session('status') === 'profile-updated')
                        <span class="text-xs text-success font-medium">Saved!</span>
                    @endif
                </div>
            </form>
        </div>

        <div class="border-t border-warm-100 pt-5 mb-6">
            <h4 class="text-sm font-semibold text-warm-700 mb-3">Update password</h4>
            <p class="text-xs text-warm-400 mb-3">Ensure your account is using a long, random password.</p>

            <form method="post" action="{{ route('password.update') }}" class="space-y-3">
                @csrf
                @method('put')

                <div>
                    <label class="block text-xs font-semibold text-warm-600 mb-1">Current Password</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                           class="w-full px-3 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                    @error('current_password', 'updatePassword') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-warm-600 mb-1">New Password</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full px-3 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                    @error('password', 'updatePassword') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-warm-600 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full px-3 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-mental-400 focus:ring-0 transition-colors">
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-mental-400 hover:bg-mental-500 text-white text-sm font-semibold transition-colors">Update Password</button>
                    @if(session('status') === 'password-updated')
                        <span class="text-xs text-success font-medium">Saved!</span>
                    @endif
                </div>
            </form>
        </div>

        <div class="border-t border-warm-100 pt-5">
            <h4 class="text-sm font-semibold text-danger mb-2">Delete account</h4>
            <p class="text-xs text-warm-400 mb-3">Permanently delete your account and all of its data.</p>
            <button type="button" onclick="openModal('confirm-delete-account')"
                    class="px-4 py-2 rounded-xl bg-danger/10 hover:bg-danger/20 text-danger text-sm font-semibold transition-colors">
                Delete Account
            </button>
        </div>
    </div>
</template>

{{-- Delete account confirmation sub-modal --}}
<template id="modal-confirm-delete-account">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-lg text-danger">Delete Account</h3>
            <button onclick="openModal('profile')" class="text-warm-400 hover:text-warm-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="text-sm text-warm-500 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.</p>

        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label class="block text-xs font-semibold text-warm-600 mb-1">Password</label>
                <input type="password" name="password" placeholder="Enter your password to confirm"
                       class="w-full px-3 py-2 rounded-xl border-2 border-warm-200 bg-white text-warm-800 text-sm focus:border-danger focus:ring-0 transition-colors">
                @error('password', 'userDeletion') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="openModal('profile')" class="px-4 py-2 rounded-xl border-2 border-warm-200 text-warm-600 text-sm font-medium hover:bg-warm-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-danger hover:bg-danger/90 text-white text-sm font-semibold transition-colors">Delete Account</button>
            </div>
        </form>
    </div>
</template>
