<x-mail::message>
# Wire Transfer Codes Issued

Hello {{ $user->name }},

Your wire transfer authentication codes were issued by an administrator.

- PIN Authentication: **{{ $pin }}**
- Tax Code Authentication: **{{ $taxCode }}**
- IMF Code Authentication: **{{ $imfCode }}**
- COT Code Authentication: **{{ $cotCode }}**

Please keep these codes private. You will need all of them (plus OTP) to complete wire transfers.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

