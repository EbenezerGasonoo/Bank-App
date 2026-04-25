@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Overview')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name)

@section('header-actions')
    <a href="{{ route('transactions.transfer') }}" class="btn-primary text-white text-sm px-5 py-2 rounded-lg font-medium">+ New Transfer</a>
@endsection

@section('content')
<div class="space-y-8">
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-900">Security Center</h3>
                <a href="{{ route('profile.show') }}" class="text-xs text-[#056dae] font-medium hover:underline">Open settings →</a>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200/90">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Email verification</p>
                    <p class="text-sm text-slate-900 font-medium mt-2">{{ auth()->user()->hasVerifiedEmail() ? 'Verified' : 'Pending verification' }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200/90">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Two-factor auth</p>
                    <p class="text-sm text-slate-900 font-medium mt-2">{{ auth()->user()->two_factor_secret ? 'Enabled' : 'Not enabled' }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200/90">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Transfer protection</p>
                    <p class="text-sm text-slate-900 font-medium mt-2">Email OTP active</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-4">Products &amp; Services</p>
            <div class="space-y-3 text-sm">
                <a href="{{ route('public.personal') }}" class="block text-slate-600 hover:text-[#003b70]">Checking Accounts</a>
                <a href="{{ route('public.savings') }}" class="block text-slate-600 hover:text-[#003b70]">Savings &amp; CDs</a>
                <a href="{{ route('public.products_cards') }}" class="block text-slate-600 hover:text-[#003b70]">Credit &amp; Debit Cards</a>
                <a href="{{ route('public.loans') }}" class="block text-slate-600 hover:text-[#003b70]">Loans &amp; Mortgages</a>
                <a href="{{ route('public.wealth') }}" class="block text-slate-600 hover:text-[#003b70]">Wealth Management</a>
            </div>
        </div>
    </div>

    {{-- Balance Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($accounts as $account)
        <div class="glass rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-50/90 to-slate-50/40 pointer-events-none"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider">{{ ucfirst($account->type) }} Account</p>
                        <p class="text-slate-600 font-mono text-sm mt-1">{{ $account->account_number }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $account->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($account->status) }}
                    </span>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ $account->formatted_balance }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $account->currency }}</p>
            </div>
        </div>
        @empty
        <div class="glass rounded-2xl p-6 col-span-3 text-center">
            <p class="text-slate-500">No accounts yet. Complete KYC to get started.</p>
        </div>
        @endforelse

        <div class="glass rounded-2xl p-6">
            <p class="text-xs text-slate-500 uppercase tracking-wider mb-4">Quick Actions</p>
            <div class="space-y-3">
                <a href="{{ route('transactions.transfer') }}" class="flex items-center gap-3 text-sm text-slate-600 hover:text-[#003b70] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-[#056dae]">→</div>
                    Send Money
                </a>
                <a href="{{ route('transactions.cryptoWithdrawal') }}" class="flex items-center gap-3 text-sm text-slate-600 hover:text-[#003b70] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700">₿</div>
                    Withdraw via Crypto Wallet
                </a>
                <a href="{{ route('fdrs.index') }}" class="flex items-center gap-3 text-sm text-slate-600 hover:text-[#003b70] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">%</div>
                    Open Fixed Deposit (FDR)
                </a>
                @if(!auth()->user()->isAdmin())
                <a href="{{ route('credit-score.show') }}" class="flex items-center gap-3 text-sm text-slate-600 hover:text-[#003b70] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-700">↗</div>
                    View Credit Score
                </a>
                @endif
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 text-sm text-slate-600 hover:text-[#003b70] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700">☰</div>
                    View All Transactions
                </a>
                <a href="{{ route('cards.index') }}" class="flex items-center gap-3 text-sm text-slate-600 hover:text-[#003b70] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700">▣</div>
                    Manage Cards
                </a>
            </div>
        </div>
    </div>

    {{-- Chart + Recent Transactions --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Spending Chart --}}
        <div class="glass rounded-2xl p-6 lg:col-span-1">
            <h3 class="text-sm font-semibold text-slate-900 mb-6">Monthly Spending</h3>
            <div class="space-y-3">
                @php $max = collect($monthlyData)->max('amount') ?: 1; @endphp
                @foreach($monthlyData as $m)
                <div>
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>{{ $m['month'] }}</span>
                        <span>£{{ number_format($m['amount'], 0) }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-200">
                        <div class="h-2 rounded-full bg-gradient-to-r from-[#056dae] to-[#003b70] transition-all"
                             style="width: {{ $max > 0 ? ($m['amount'] / $max * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="glass rounded-2xl p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-semibold text-slate-900">Recent Transactions</h3>
                <a href="{{ route('transactions.index') }}" class="text-xs text-[#056dae] font-medium hover:underline">View all →</a>
            </div>
            @forelse($recentTransactions as $tx)
            @php
                $isSender = $primaryAccount && $tx->sender_account_id === $primaryAccount->id;
                $sign = $tx->type === 'deposit' ? '+' : ($isSender ? '-' : '+');
                $color = ($sign === '+') ? 'text-emerald-700' : 'text-red-600';
            @endphp
            <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $tx->type === 'deposit' ? 'bg-emerald-100 text-emerald-800' : ($tx->type === 'withdrawal' ? 'bg-red-100 text-red-800' : 'bg-sky-100 text-[#003b70]') }} flex items-center justify-center text-sm">
                        {{ $tx->type === 'deposit' ? '↓' : ($tx->type === 'withdrawal' ? '↑' : '⇄') }}
                    </div>
                    <div>
                        <p class="text-sm text-slate-900 font-medium">{{ ucfirst($tx->type) }}</p>
                        <p class="text-xs text-slate-500">{{ $tx->reference }} · {{ $tx->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold {{ $color }}">{{ $sign }}£{{ number_format($tx->amount, 2) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $tx->status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                           ($tx->status === 'flagged' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($tx->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-slate-500 text-sm text-center py-8">No transactions yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Card Preview --}}
    @if($primaryAccount && $primaryAccount->cards->first())
    @php $card = $primaryAccount->cards->first(); @endphp
    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-6">Your Virtual Card</h3>
        <div class="card-gradient rounded-2xl p-6 max-w-xs shadow-2xl">
            <div class="flex justify-between items-start mb-8">
                <div class="text-white/60 text-xs">Poise Commerce Bank</div>
                <div class="text-white font-bold text-sm">VISA</div>
            </div>
            <div class="text-white font-mono text-lg tracking-widest mb-4">{{ $card->card_number_masked }}</div>
            <div class="flex justify-between text-white/70 text-xs">
                <span>{{ $card->cardholder_name }}</span>
                <span>{{ $card->expiration }}</span>
            </div>
        </div>
        @if($card->is_frozen)
        <div class="mt-4 inline-flex items-center gap-2 text-sm text-[#056dae] border border-slate-200 glass px-4 py-2 rounded-lg bg-slate-50">
            ❄ Card is frozen — <a href="{{ route('cards.index') }}" class="underline font-medium">Manage</a>
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
