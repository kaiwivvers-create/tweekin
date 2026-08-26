@extends('layouts.app')

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-8 sm:py-10">
    <h1 class="font-display font-bold text-2xl sm:text-3xl text-warm-800 mb-2">Settings</h1>
    <p class="text-warm-500 mb-8">Manage your account settings and preferences.</p>

    <div class="space-y-6 max-w-2xl">
        {{-- Profile Information --}}
        <div class="bg-white rounded-2xl border border-warm-200 shadow-sm p-6">
            <h2 class="font-semibold text-warm-800 mb-4">Profile information</h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="bg-white rounded-2xl border border-warm-200 shadow-sm p-6">
            <h2 class="font-semibold text-warm-800 mb-4">Update password</h2>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="bg-white rounded-2xl border border-danger/30 shadow-sm p-6">
            <h2 class="font-semibold text-danger mb-4">Delete account</h2>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection

@section('footer')
@include('partials.footer')
@endsection
