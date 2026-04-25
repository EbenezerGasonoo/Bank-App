@extends('layouts.app')
@section('title', 'Verify Transfer')
@section('page-title', 'Verify Local Transfer')
@section('page-subtitle', 'Complete OTP and wire authentication checks')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="glass rounded-2xl p-8">
            @if(!empty($notice))
                <p class="text-sm text-emerald-800 mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200">{{ $notice }}</p>
            @endif

            <div class="text-sm text-slate-600 mb-6 space-y-2">
                <p><span class="text-slate-500">From:</span> Account {{ $pendingTransfer['sender_account_number'] }}</p>
                <p><span class="text-slate-500">To:</span> {{ $pendingTransfer['receiver_account_name'] }} ({{ $pendingTransfer['receiver_account_number'] }})</p>
                <p><span class="text-slate-500">Amount:</span> GBP {{ number_format($pendingTransfer['amount'], 2) }}</p>
                <p><span class="text-slate-500">Reference:</span> {{ $pendingTransfer['description'] }}</p>
            </div>

            <form method="POST" action="{{ route('transactions.doVerifyTransfer') }}" class="space-y-6">
                @csrf
                <div>
                    <label>Verification Code</label>
                    <input type="text" name="otp" inputmode="numeric" maxlength="6" placeholder="6-digit code" required value="{{ old('otp') }}" />
                    @error('otp')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label>PIN Authentication</label>
                    <input type="password" name="pin" placeholder="Enter transfer PIN" required value="{{ old('pin') }}" />
                    @error('pin')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label>Tax Code Authentication</label>
                    <input type="text" name="tax_code" placeholder="Enter tax code" required value="{{ old('tax_code') }}" />
                    @error('tax_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label>IMF Code Authentication</label>
                    <input type="text" name="imf_code" placeholder="Enter IMF code" required value="{{ old('imf_code') }}" />
                    @error('imf_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label>COT Code Authentication</label>
                    <input type="text" name="cot_code" placeholder="Enter COT code" required value="{{ old('cot_code') }}" />
                    @error('cot_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-primary w-full text-white py-3 rounded-lg font-semibold text-base mt-2">
                    Confirm Transfer
                </button>
            </form>
        </div>
        <p class="text-center text-xs text-slate-500 mt-4">OTP expires in 10 minutes. All transfer authentications are required.</p>
    </div>
    <div class="space-y-6">
        <div class="glass rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Verification Checklist</h3>
            <div class="space-y-3 text-sm text-slate-600">
                <p>Confirm account number and recipient name match your intent.</p>
                <p>Use your OTP, transfer PIN, Tax Code, IMF Code, and COT Code.</p>
                <p>If details are wrong, return and start the transfer again.</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-4">Need help now?</p>
            <div class="space-y-3 text-sm">
                <a href="{{ route('public.customer_service') }}" class="block text-slate-600 hover:text-[#003b70]">Contact customer service</a>
                <a href="{{ route('profile.show') }}" class="block text-slate-600 hover:text-[#003b70]">Review security settings</a>
                <a href="{{ route('transactions.index') }}" class="block text-slate-600 hover:text-[#003b70]">Recent transactions</a>
            </div>
        </div>
    </div>
</div>
@endsection
