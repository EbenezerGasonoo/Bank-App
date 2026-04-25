@extends('layouts.app')
@section('title', 'Cards')
@section('page-title', 'Virtual Cards')

@section('content')
<div class="space-y-8">
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-6 lg:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Card Security Center</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200/90">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Freeze control</p>
                    <p class="text-sm text-slate-900 font-medium mt-2">Instantly block transactions when needed</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200/90">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Alerts</p>
                    <p class="text-sm text-slate-900 font-medium mt-2">Real-time notifications for card activity</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200/90">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Disputes</p>
                    <p class="text-sm text-slate-900 font-medium mt-2">Contact support for unauthorized charges</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-4">Card services</p>
            <div class="space-y-3 text-sm">
                <a href="{{ route('transactions.transfer') }}" class="block text-slate-600 hover:text-[#003b70]">Send money</a>
                <a href="{{ route('transactions.index') }}" class="block text-slate-600 hover:text-[#003b70]">Transaction history</a>
                <a href="{{ route('profile.show') }}" class="block text-slate-600 hover:text-[#003b70]">Profile and security</a>
                <a href="{{ route('dashboard') }}" class="block text-slate-600 hover:text-[#003b70]">Account overview</a>
            </div>
        </div>
    </div>

    @forelse($accounts as $account)
    @forelse($account->cards as $card)
    <div class="glass rounded-2xl p-6 max-w-2xl">
        <div class="flex flex-col md:flex-row gap-8 items-start">
            {{-- Card Visual --}}
            <div class="card-gradient rounded-2xl p-6 w-72 flex-shrink-0 shadow-2xl {{ $card->is_frozen ? 'opacity-50 grayscale' : '' }}">
                <div class="flex justify-between items-start mb-8">
                    <div class="text-white/60 text-xs">Poise Commerce Bank</div>
                    <div class="text-white font-bold text-sm">VISA</div>
                </div>
                @if($card->is_frozen)
                <div class="text-center text-white/80 text-sm font-semibold mb-4">❄ FROZEN</div>
                @endif
                <div class="text-white font-mono text-lg tracking-widest mb-4">{{ $card->card_number_masked }}</div>
                <div class="flex justify-between text-white/70 text-xs">
                    <span>{{ $card->cardholder_name }}</span>
                    <span>{{ $card->expiration }}</span>
                </div>
            </div>

            {{-- Card Details & Actions --}}
            <div class="flex-1">
                <h3 class="text-slate-900 font-semibold mb-1">Virtual Debit Card</h3>
                <p class="text-xs text-slate-500 mb-6">Account: {{ $account->account_number }}</p>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="glass rounded-xl p-4">
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="text-sm font-medium {{ $card->is_frozen ? 'text-[#056dae]' : 'text-emerald-700' }} mt-1">
                            {{ $card->is_frozen ? '❄ Frozen' : '✓ Active' }}
                        </p>
                    </div>
                    <div class="glass rounded-xl p-4">
                        <p class="text-xs text-slate-500">Expires</p>
                        <p class="text-sm font-medium text-slate-900 mt-1">{{ $card->expiration }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cards.toggleFreeze', $card) }}">
                    @csrf
                    <button type="submit" class="w-full {{ $card->is_frozen ? 'btn-success' : 'btn-danger' }} text-white py-3 rounded-lg font-semibold text-sm transition-all">
                        {{ $card->is_frozen ? '✓ Unfreeze Card' : '❄ Freeze Card' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
        <div class="glass rounded-2xl p-8 text-center text-slate-500">No cards issued for this account.</div>
    @endforelse
    @empty
    <div class="glass rounded-2xl p-8 text-center text-slate-500">
        <p>No accounts with cards found.</p>
    </div>
    @endforelse
</div>
@endsection
