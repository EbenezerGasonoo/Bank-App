<x-mail::message>
# Login Verification Code

Hello {{ $user->name }},

Use the one-time passcode below to complete your sign-in:

<x-mail::panel>
{{ $otpCode }}
</x-mail::panel>

This code expires in **{{ $expiryMinutes }} minutes**.

If this wasn't you, secure your password immediately and contact support.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

