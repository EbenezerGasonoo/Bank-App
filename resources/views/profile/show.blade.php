@extends('layouts.app')
@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', auth()->user()->email)

@section('content')
<div class="max-w-5xl space-y-6">
    <div class="grid md:grid-cols-3 gap-4">
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-profile-stat">
            <p class="text-xs text-slate-500 uppercase tracking-wide">KYC Status</p>
            <p class="text-sm font-semibold mt-1 {{ auth()->user()->kyc_status === 'approved' ? 'text-emerald-700' : (auth()->user()->kyc_status === 'rejected' ? 'text-red-700' : 'text-amber-700') }}">
                {{ ucfirst(auth()->user()->kyc_status) }}
            </p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-profile-stat">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Account Status</p>
            <p class="text-sm font-semibold mt-1 {{ auth()->user()->account_status === 'active' ? 'text-emerald-700' : 'text-red-700' }}">
                {{ ucfirst(auth()->user()->account_status) }}
            </p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-profile-stat">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Profile Completion</p>
            @php
                $completeFields = collect([
                    auth()->user()->name,
                    auth()->user()->email,
                    auth()->user()->phone,
                    auth()->user()->date_of_birth,
                    auth()->user()->address,
                ])->filter(fn ($value) => !blank($value))->count();
                $completionPercent = (int) round(($completeFields / 5) * 100);
            @endphp
            <p class="text-sm font-semibold mt-1 text-slate-900">{{ $completionPercent }}%</p>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 border border-slate-200 pc-profile-card">
        <h3 class="text-base font-semibold text-slate-900">Profile Information</h3>
        <p class="text-xs text-slate-500 mt-1">Update your account information and contact details.</p>

        @if (session('status') === 'profile-information-updated')
            <p class="text-xs text-emerald-700 mt-3">Profile information updated successfully.</p>
        @endif

        <form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-5 mt-5">
            @csrf
            @method('PUT')

            <label class="pc-profile-field">
                <span class="pc-profile-label">Name</span>
                <input name="name" type="text" class="mt-1" value="{{ old('name', auth()->user()->name) }}" required />
                @error('name', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">Email</span>
                <input name="email" type="email" class="mt-1" value="{{ old('email', auth()->user()->email) }}" required />
                @error('email', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">Phone</span>
                <input name="phone" type="text" class="mt-1" value="{{ old('phone', auth()->user()->phone) }}" placeholder="+1 000 000 0000" />
                @error('phone', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">Date of Birth</span>
                <input name="date_of_birth" type="date" class="mt-1" value="{{ old('date_of_birth', optional(auth()->user()->date_of_birth)->format('Y-m-d')) }}" />
                @error('date_of_birth', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="md:col-span-2 pc-profile-field">
                <span class="pc-profile-label">Address</span>
                <textarea name="address" rows="3" class="mt-1">{{ old('address', auth()->user()->address) }}</textarea>
                @error('address', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">Profile Photo</span>
                <input name="photo" type="file" accept=".jpg,.jpeg,.png" class="mt-1 pc-profile-file" />
                <span class="text-[11px] text-slate-500 block mt-1">JPG/PNG up to 1MB.</span>
                @error('photo', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">ID Document</span>
                <input name="id_document" type="file" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 pc-profile-file" />
                <span class="text-[11px] text-slate-500 block mt-1">JPG/PNG/PDF up to 5MB.</span>
                @error('id_document', 'updateProfileInformation')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
                @if(auth()->user()->id_document_path)
                    <a href="{{ Storage::url(auth()->user()->id_document_path) }}" target="_blank" class="text-xs text-[#056dae] hover:underline inline-block mt-1">View current ID document</a>
                @endif
            </label>

            <div class="md:col-span-2 pt-1">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Save Profile</button>
            </div>
        </form>
    </div>

    <div class="glass rounded-2xl p-6 border border-slate-200 pc-profile-card">
        <h3 class="text-base font-semibold text-slate-900">Update Password</h3>
        <p class="text-xs text-slate-500 mt-1">Use a strong password to secure your account.</p>

        @if (session('status') === 'password-updated')
            <p class="text-xs text-emerald-700 mt-3">Password updated successfully.</p>
        @endif

        <form method="POST" action="{{ route('user-password.update') }}" class="grid md:grid-cols-2 gap-5 mt-5">
            @csrf
            @method('PUT')

            <label class="md:col-span-2 pc-profile-field">
                <span class="pc-profile-label">Current Password</span>
                <input name="current_password" type="password" class="mt-1" required />
                @error('current_password', 'updatePassword')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">New Password</span>
                <input name="password" type="password" class="mt-1" required />
                @error('password', 'updatePassword')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </label>

            <label class="pc-profile-field">
                <span class="pc-profile-label">Confirm New Password</span>
                <input name="password_confirmation" type="password" class="mt-1" required />
            </label>

            <div class="md:col-span-2 pt-1">
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
