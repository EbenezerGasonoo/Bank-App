@extends('layouts.app')
@section('title', 'Transactions')
@section('page-title', 'Transaction History')
@section('page-subtitle', request('type') === 'deposit' ? 'Deposits Overview' : 'Transfers, Deposits & Withdrawals')

@section('header-actions')
    <a href="{{ route('transactions.transfer') }}" class="btn-primary text-white text-sm px-5 py-2 rounded-lg font-medium">+ Transfer</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="glass rounded-2xl p-5 border border-slate-200">
        <form method="GET" class="grid md:grid-cols-[minmax(0,1fr)_160px_170px_auto] gap-3 items-end">
            <label>
                <span class="text-xs text-slate-600">Search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Reference or description..." class="mt-1" />
            </label>
            <label>
                <span class="text-xs text-slate-600">Type</span>
                <select name="type" class="mt-1">
                    <option value="">All Types</option>
                    <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                    <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </label>
            <label>
                <span class="text-xs text-slate-600">Status</span>
                <select name="status" class="mt-1">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Flagged</option>
                </select>
            </label>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary text-white px-4 py-2.5 rounded-lg text-sm">Apply</button>
                <a href="{{ route('transactions.index') }}" class="px-4 py-2.5 rounded-lg text-sm border border-slate-300 text-slate-700 bg-white">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Records</p>
            <p class="text-xl font-semibold text-slate-900 mt-1">{{ $txSummary['count'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Credits</p>
            <p class="text-xl font-semibold text-emerald-700 mt-1">+£{{ number_format($txSummary['credit_total'], 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Debits</p>
            <p class="text-xl font-semibold text-red-600 mt-1">-£{{ number_format($txSummary['debit_total'], 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Net</p>
            <p class="text-xl font-semibold {{ $txSummary['net_total'] >= 0 ? 'text-emerald-700' : 'text-red-600' }} mt-1">
                {{ $txSummary['net_total'] >= 0 ? '+' : '-' }}£{{ number_format(abs($txSummary['net_total']), 2) }}
            </p>
        </div>
    </div>

    @if(request('type') === 'deposit' || request()->routeIs('transactions.index'))
    <div class="glass rounded-2xl p-5 border border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Request Deposit Approval</h3>
                <p class="text-xs text-slate-500 mt-1">Submit a deposit request for admin review and posting.</p>
            </div>
            <span class="pc-statement-pill is-status">Approval Required</span>
        </div>
        <form method="POST" action="{{ route('transactions.requestDeposit') }}" class="grid md:grid-cols-[minmax(0,1fr)_180px_minmax(0,1fr)_auto] gap-3 items-end">
            @csrf
            <label>
                <span class="text-xs text-slate-600">Account</span>
                <select name="account_id" class="mt-1" required>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ ucfirst($account->type) }} — {{ $account->account_number }} ({{ $account->formatted_balance }})
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="text-xs text-slate-600">Amount (£)</span>
                <input name="amount" type="number" min="10" step="0.01" class="mt-1" placeholder="0.00" required />
            </label>
            <label>
                <span class="text-xs text-slate-600">Description (optional)</span>
                <input name="description" type="text" class="mt-1" placeholder="e.g. Cash branch deposit" />
            </label>
            <button type="submit" class="btn-primary text-white px-4 py-2.5 rounded-lg text-sm">Request</button>
        </form>
    </div>
    @endif

    <div class="glass rounded-2xl overflow-hidden border border-slate-200 pc-statement-shell">
        <div class="overflow-x-auto">
        <table class="pc-statement-table min-w-[760px]">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>From / To</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                @php
                    $isSender = $accountIds->contains($tx->sender_account_id);
                    $sign = $tx->type === 'deposit' ? '+' : ($isSender ? '-' : '+');
                    $color = ($sign === '+') ? 'text-emerald-700' : 'text-red-600';
                @endphp
                <tr>
                    <td class="font-mono text-[11px] text-slate-500">{{ $tx->reference }}</td>
                    <td>
                        <span class="pc-statement-pill {{ $tx->type === 'deposit' ? 'is-deposit' : ($tx->type === 'transfer' ? 'is-transfer' : 'is-withdrawal') }}">
                            {{ ucfirst($tx->type) }}
                        </span>
                    </td>
                    <td class="text-slate-600 text-sm">
                        @if($tx->type === 'transfer')
                            {{ optional(optional($tx->senderAccount)->user)->name ?? 'Unknown' }}
                            → {{ optional(optional($tx->receiverAccount)->user)->name ?? 'Unknown' }}
                        @else
                            {{ ucfirst($tx->type) }}
                        @endif
                    </td>
                    <td class="font-semibold {{ $color }}">{{ $sign }}£{{ number_format((float) $tx->amount, 2) }}</td>
                    <td>
                        <span class="pc-statement-pill {{ $tx->status === 'completed' ? 'is-completed' : 'is-status' }}">
                            {{ ucfirst($tx->status) }}
                        </span>
                    </td>
                    <td class="text-slate-500 text-xs">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-slate-500">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
