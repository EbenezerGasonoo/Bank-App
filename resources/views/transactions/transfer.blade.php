@extends('layouts.app')
@section('title', 'Transfer Funds')
@section('page-title', 'Transfer Funds')
@section('page-subtitle', 'Local and international transfers')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="glass rounded-2xl p-8">
            <form
                method="POST"
                action="{{ route('transactions.doTransfer') }}"
                class="space-y-6"
                x-data="{
                    mode: '{{ old('transfer_mode', 'local') }}',
                    intlPin: '{{ old('pin') }}',
                    intlTax: '{{ old('tax_code') }}',
                    intlImf: '{{ old('imf_code') }}',
                    intlCot: '{{ old('cot_code') }}',
                    canSubmitInternational() {
                        return this.intlPin.trim() !== '' &&
                            this.intlTax.trim() !== '' &&
                            this.intlImf.trim() !== '' &&
                            this.intlCot.trim() !== '';
                    }
                }"
            >
                @csrf

                <div>
                    <label class="mb-2">Transfer Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="mode='local'" :class="mode==='local' ? 'border-[#056dae] bg-sky-50 text-[#003b70]' : 'border-slate-200 text-slate-600'" class="rounded-xl border px-4 py-3 text-sm font-semibold transition-all">
                            Local Transfer
                        </button>
                        <button type="button" @click="mode='international'" :class="mode==='international' ? 'border-[#056dae] bg-sky-50 text-[#003b70]' : 'border-slate-200 text-slate-600'" class="rounded-xl border px-4 py-3 text-sm font-semibold transition-all">
                            International Transfer
                        </button>
                    </div>
                    <input type="hidden" name="transfer_mode" :value="mode" />
                    @error('transfer_mode')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label>From Account</label>
                    <select name="sender_account_id" required>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ (string) old('sender_account_id') === (string) $acc->id ? 'selected' : '' }}>
                            {{ ucfirst($acc->type) }} — {{ $acc->account_number }} (Balance: {{ $acc->formatted_balance }})
                        </option>
                        @endforeach
                    </select>
                    @error('sender_account_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div x-show="mode === 'local'">
                    <label>Recipient Account Number</label>
                    <input type="text" name="receiver_account_number" placeholder="e.g. 2012345678" x-bind:required="mode === 'local'" x-bind:disabled="mode !== 'local'" value="{{ old('receiver_account_number') }}" />
                    @error('receiver_account_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div x-show="mode === 'international'" class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label>Beneficiary Name</label>
                        <input type="text" name="beneficiary_name" placeholder="Full name" value="{{ old('beneficiary_name') }}" />
                        @error('beneficiary_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>Beneficiary Bank</label>
                        <input type="text" name="beneficiary_bank" placeholder="Bank name" value="{{ old('beneficiary_bank') }}" />
                        @error('beneficiary_bank')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>Account/IBAN</label>
                        <input type="text" name="beneficiary_account_number" placeholder="IBAN or account number" value="{{ old('beneficiary_account_number') }}" />
                        @error('beneficiary_account_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>SWIFT/BIC</label>
                        <input type="text" name="swift_code" placeholder="SWIFT code" value="{{ old('swift_code') }}" />
                        @error('swift_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror

                        <button
                            type="button"
                            class="mt-3 glass border border-slate-200 text-[#003b70] px-4 py-2 rounded-lg text-xs font-semibold hover:border-[#056dae] transition-all"
                            @click="document.getElementById('request-wire-codes-inline').submit()"
                        >
                            Request Email Code from Bank
                        </button>
                    </div>
                    <div class="md:col-span-2">
                        <label>Beneficiary Country</label>
                        <input type="text" name="beneficiary_country" placeholder="Country" value="{{ old('beneficiary_country') }}" />
                        @error('beneficiary_country')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>Transfer PIN</label>
                        <input type="text" name="pin" x-model="intlPin" placeholder="Enter PIN" />
                        @error('pin')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>Tax Code</label>
                        <input type="text" name="tax_code" x-model="intlTax" placeholder="Enter Tax code" />
                        @error('tax_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>IMF Code</label>
                        <input type="text" name="imf_code" x-model="intlImf" placeholder="Enter IMF code" />
                        @error('imf_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label>COT Code</label>
                        <input type="text" name="cot_code" x-model="intlCot" placeholder="Enter COT code" />
                        @error('cot_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label>Amount (£)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required value="{{ old('amount') }}" />
                    @error('amount')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label x-show="mode === 'local'">Description (optional)</label>
                    <label x-show="mode === 'international'">Purpose / Narrative (optional)</label>
                    <input type="text" name="description" placeholder="e.g. Rent payment / tuition fee" value="{{ old('description') }}" />
                </div>

                <button
                    type="submit"
                    class="btn-primary w-full text-white py-3 rounded-lg font-semibold text-base mt-2"
                    :disabled="mode === 'international' && !canSubmitInternational()"
                    :class="{ 'opacity-50 cursor-not-allowed': mode === 'international' && !canSubmitInternational() }"
                >
                    <span x-show="mode === 'local'">Continue to Local Verification →</span>
                    <span x-show="mode === 'international'">Submit International Transfer →</span>
                </button>
            </form>
        </div>
        <p class="text-center text-xs text-slate-500 mt-4">
            Local transfers: OTP + PIN + Tax Code + IMF Code + COT Code. International transfers are submitted for review.
        </p>
        <form method="POST" action="{{ route('transactions.requestWireCodes') }}" class="mt-4" id="request-wire-codes-inline">
            @csrf
            <button type="submit" class="w-full glass border border-slate-200 text-[#003b70] py-2.5 rounded-lg text-sm font-semibold hover:border-[#056dae] transition-all">
                Request Email Code from Bank
            </button>
        </form>
    </div>
    <div class="space-y-6">
        <div class="glass rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Transfer Security</h3>
            <div class="space-y-3 text-sm text-slate-600">
                <p>Local: instant transfer after OTP and all wire auth codes are validated.</p>
                <p>International: beneficiary and SWIFT details are reviewed before processing.</p>
                <p>Request wire codes from admins if your transfer auth is not yet issued.</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-4">Payment shortcuts</p>
            <div class="space-y-3 text-sm">
                <a href="{{ route('transactions.index') }}" class="block text-slate-600 hover:text-[#003b70]">View transaction history</a>
                <a href="{{ route('transactions.cryptoWithdrawal') }}" class="block text-slate-600 hover:text-[#003b70]">Withdraw via crypto wallet</a>
                <a href="{{ route('cards.index') }}" class="block text-slate-600 hover:text-[#003b70]">Card controls</a>
                <a href="{{ route('profile.show') }}" class="block text-slate-600 hover:text-[#003b70]">Security settings</a>
            </div>
        </div>
    </div>
</div>
@endsection
