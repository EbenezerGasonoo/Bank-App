<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <div class="mb-6 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Email OTP Verification</h1>
            <p class="text-sm text-gray-600 mt-1">Enter the 6-digit code sent to your email to complete sign-in.</p>
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login.otp.verify') }}">
            @csrf
            <div>
                <x-label for="otp" value="{{ __('One-Time Passcode') }}" />
                <x-input id="otp" class="block mt-1 w-full" type="text" name="otp" maxlength="6" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Verify & Continue') }}
                </x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('login.otp.resend') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                Resend OTP
            </button>
        </form>
    </x-authentication-card>
</x-guest-layout>

