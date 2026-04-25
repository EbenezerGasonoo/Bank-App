<x-mail::message>
# Credit Received

Hello {{ $user->name }},

A credit has been posted to your account.

- Account: **{{ strtoupper($account->type) }}** ({{ $account->account_number }})
- Amount: **GBP {{ number_format((float) $transaction->amount, 2) }}**
- Reference: **{{ $transaction->reference }}**
- Date: **{{ $transaction->created_at->format('d M Y, H:i') }}**
- Description: **{{ $transaction->description ?: 'Account credit' }}**

Your latest available balance is **GBP {{ number_format((float) $account->fresh()->balance, 2) }}**.

If you do not recognize this activity, please contact support immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

