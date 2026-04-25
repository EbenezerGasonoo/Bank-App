<x-mail::message>
# Account Suspended

Hello {{ $user->name }},

Your account has been temporarily suspended by the bank compliance team.

This can happen when additional verification or account review is required.

<x-mail::button :url="url('/support')">
Contact Support
</x-mail::button>

If you believe this is an error, please contact support and include your registered email address for a faster review.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

