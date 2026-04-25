<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />
        <div class="mb-6 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Sign on</h1>
            <p class="text-sm text-gray-600 mt-1">Secure access to your Poise Commerce Bank accounts and payments.</p>
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('User ID (Email)') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember this device') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot User ID / Password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
            <div class="mt-5 text-center text-sm text-gray-600">
                No account?
                <a href="{{ route('register') }}" class="underline hover:text-gray-900">Create one</a>
            </div>
        </form>
        <div class="mt-8 border-t pt-4 text-center text-xs text-gray-500 space-x-3">
            <a href="{{ route('public.support') }}" class="hover:text-gray-700">Support</a>
            <a href="{{ route('public.security_center') }}" class="hover:text-gray-700">Security</a>
            <a href="{{ route('public.faq') }}" class="hover:text-gray-700">FAQ</a>
        </div>
    </x-authentication-card>
</x-guest-layout>
