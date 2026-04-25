@extends('layouts.app')
@section('title', 'Statement')
@section('page-title', 'Statement')
@section('page-subtitle', 'Account Statement & Export')

@section('content')
<div class="space-y-6 pc-statement-page">
    <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-filter-card">
        <form method="GET" class="grid md:grid-cols-2 gap-4 items-end">
            <label>
                <span class="text-xs text-slate-600">Filter By Period <span class="text-red-500">*</span></span>
                <input
                    type="text"
                    name="period"
                    value="{{ request('period') }}"
                    placeholder="Start Date - End Date"
                    class="mt-1"
                />
            </label>
            <label>
                <span class="text-xs text-slate-600">Type</span>
                <select name="type" class="mt-1">
                    <option value="">All</option>
                    <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                    <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </label>
            <label>
                <span class="text-xs text-slate-600">From Amount</span>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="from_amount"
                    value="{{ request('from_amount') }}"
                    placeholder="Enter Amount"
                    class="mt-1"
                />
            </label>
            <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-3 items-end">
                <label>
                    <span class="text-xs text-slate-600">To Amount</span>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="to_amount"
                        value="{{ request('to_amount') }}"
                        placeholder="Enter Amount"
                        class="mt-1"
                    />
                </label>
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-lg text-sm mb-[1px]">Filter</button>
            </div>
            <input type="hidden" name="status" value="{{ request('status') }}" />
        </form>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-kpi">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Credits</p>
            <p class="text-xl font-semibold text-emerald-700 mt-1">+£{{ number_format($creditTotal, 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-kpi">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Debits</p>
            <p class="text-xl font-semibold text-red-600 mt-1">-£{{ number_format($debitTotal, 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-kpi">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Net</p>
            <p class="text-xl font-semibold {{ $netTotal >= 0 ? 'text-emerald-700' : 'text-red-600' }} mt-1">
                {{ $netTotal >= 0 ? '+' : '-' }}£{{ number_format(abs($netTotal), 2) }}
            </p>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('statement.export', request()->query()) }}" class="px-4 py-2.5 rounded-lg text-sm border border-slate-300 text-slate-700 bg-white text-center hover:bg-slate-50 transition-colors">Export CSV</a>
    </div>

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
                        $isCredit = $tx->type === 'deposit' || $accountIds->contains($tx->receiver_account_id);
                    @endphp
                    <tr>
                        <td class="font-mono text-[11px] text-slate-500">{{ $tx->reference }}</td>
                        <td>
                            <span class="pc-statement-pill {{ $tx->type === 'deposit' ? 'is-deposit' : ($tx->type === 'transfer' ? 'is-transfer' : 'is-withdrawal') }}">
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>
                        <td class="text-sm text-slate-700">
                            @if($tx->type === 'transfer')
                                {{ optional(optional($tx->senderAccount)->user)->name ?? 'Unknown' }}
                                → {{ optional(optional($tx->receiverAccount)->user)->name ?? 'Unknown' }}
                            @else
                                {{ ucfirst($tx->type) }}
                            @endif
                        </td>
                        <td>
                            <span class="font-semibold {{ $isCredit ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $isCredit ? '+' : '-' }}£{{ number_format((float) $tx->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="pc-statement-pill {{ $tx->status === 'completed' ? 'is-completed' : 'is-status' }}">{{ ucfirst($tx->status) }}</span>
                        </td>
                        <td class="text-xs text-slate-600">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-500">No statement records found for this filter.</td>
                    </tr>
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
