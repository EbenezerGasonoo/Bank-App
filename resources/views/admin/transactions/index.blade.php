@extends('layouts.app')
@section('title', 'Transactions — Admin')
@section('page-title', 'All Transactions')

@section('content')
<div class="pc-admin-overview space-y-5">
    <section class="glass rounded-2xl p-4 sm:p-5 border border-slate-200">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-900">Transaction Operations</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Track transfers, withdrawals, and deposits from one control room.</p>
            </div>
            <span class="pc-pill pc-pill-info">Live Monitoring</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
            <div class="pc-admin-kpi-card">
                <p class="pc-admin-kpi-label">Transfers</p>
                <p class="pc-admin-kpi-value">{{ $txCounts['all_transfers'] ?? 0 }}</p>
            </div>
            <div class="pc-admin-kpi-card">
                <p class="pc-admin-kpi-label">Pending W/D</p>
                <p class="pc-admin-kpi-value">{{ $txCounts['pending_withdrawals'] ?? 0 }}</p>
            </div>
            <div class="pc-admin-kpi-card">
                <p class="pc-admin-kpi-label">Deposits</p>
                <p class="pc-admin-kpi-value">{{ $txCounts['all_deposits'] ?? 0 }}</p>
            </div>
            <div class="pc-admin-kpi-card">
                <p class="pc-admin-kpi-label">Rejected</p>
                <p class="pc-admin-kpi-value">{{ ($txCounts['rejected_transfers'] ?? 0) + ($txCounts['rejected_withdrawals'] ?? 0) }}</p>
            </div>
        </div>
    </section>

    <div class="pc-admin-shell-grid">
        <div class="pc-admin-rail space-y-5">
            <aside class="pc-admin-manage-panel rounded-2xl overflow-hidden self-start">
                <div class="pc-admin-manage-header">
                    <span class="text-sm font-semibold">Money Transfers</span>
                    <span class="pc-admin-manage-badge">!</span>
                </div>
                <div class="pc-admin-manage-group">
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['transfer_filter' => 'pending'])) }}" class="pc-admin-manage-item {{ request('transfer_filter') === 'pending' ? 'is-active' : '' }}">
                        Pending Transfers
                        <span class="pc-admin-count">{{ $txCounts['pending_transfers'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['transfer_filter' => 'rejected'])) }}" class="pc-admin-manage-item {{ request('transfer_filter') === 'rejected' ? 'is-active' : '' }}">
                        Rejected Transfers
                        <span class="pc-admin-count">{{ $txCounts['rejected_transfers'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['transfer_filter' => 'own_bank'])) }}" class="pc-admin-manage-item {{ request('transfer_filter') === 'own_bank' ? 'is-active' : '' }}">
                        Own Bank Transfers
                        <span class="pc-admin-count">{{ $txCounts['own_bank_transfers'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['transfer_filter' => 'other_bank'])) }}" class="pc-admin-manage-item {{ request('transfer_filter') === 'other_bank' ? 'is-active' : '' }}">
                        Other Bank Transfers
                        <span class="pc-admin-count">{{ $txCounts['other_bank_transfers'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['transfer_filter' => 'wire'])) }}" class="pc-admin-manage-item {{ request('transfer_filter') === 'wire' ? 'is-active' : '' }}">
                        Wire Transfers
                        <span class="pc-admin-count">{{ $txCounts['wire_transfers'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['transfer_filter' => 'all'])) }}" class="pc-admin-manage-item {{ request('transfer_filter') === 'all' ? 'is-active' : '' }}">
                        All Transfers
                        <span class="pc-admin-count">{{ $txCounts['all_transfers'] ?? 0 }}</span>
                    </a>
                </div>
            </aside>

            <aside class="pc-admin-manage-panel rounded-2xl overflow-hidden self-start">
                <div class="pc-admin-manage-header">
                    <span class="text-sm font-semibold">Withdrawals</span>
                    <span class="pc-admin-manage-badge">!</span>
                </div>
                <div class="pc-admin-manage-group">
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['withdrawal_filter' => 'pending'])) }}" class="pc-admin-manage-item {{ request('withdrawal_filter') === 'pending' ? 'is-active' : '' }}">
                        Pending Withdrawals
                        <span class="pc-admin-count">{{ $txCounts['pending_withdrawals'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['withdrawal_filter' => 'approved'])) }}" class="pc-admin-manage-item {{ request('withdrawal_filter') === 'approved' ? 'is-active' : '' }}">
                        Approved Withdrawals
                        <span class="pc-admin-count">{{ $txCounts['approved_withdrawals'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['withdrawal_filter' => 'rejected'])) }}" class="pc-admin-manage-item {{ request('withdrawal_filter') === 'rejected' ? 'is-active' : '' }}">
                        Rejected Withdrawals
                        <span class="pc-admin-count">{{ $txCounts['rejected_withdrawals'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'type', 'status']), ['withdrawal_filter' => 'all'])) }}" class="pc-admin-manage-item {{ request('withdrawal_filter') === 'all' ? 'is-active' : '' }}">
                        All Withdrawals
                        <span class="pc-admin-count">{{ $txCounts['all_withdrawals'] ?? 0 }}</span>
                    </a>
                </div>
            </aside>

            <aside class="pc-admin-manage-panel rounded-2xl overflow-hidden self-start">
                <div class="pc-admin-manage-header">
                    <span class="text-sm font-semibold">Deposits</span>
                    <span class="pc-admin-manage-badge">!</span>
                </div>
                <div class="pc-admin-manage-group">
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'deposit_filter', 'type', 'status']), ['deposit_filter' => 'pending'])) }}" class="pc-admin-manage-item {{ request('deposit_filter') === 'pending' ? 'is-active' : '' }}">
                        Pending Deposits
                        <span class="pc-admin-count">{{ $txCounts['pending_deposits'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'deposit_filter', 'type', 'status']), ['deposit_filter' => 'approved'])) }}" class="pc-admin-manage-item {{ request('deposit_filter') === 'approved' ? 'is-active' : '' }}">
                        Approved Deposits
                        <span class="pc-admin-count">{{ $txCounts['approved_deposits'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'deposit_filter', 'type', 'status']), ['deposit_filter' => 'successful'])) }}" class="pc-admin-manage-item {{ request('deposit_filter') === 'successful' ? 'is-active' : '' }}">
                        Successful Deposits
                        <span class="pc-admin-count">{{ $txCounts['successful_deposits'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'deposit_filter', 'type', 'status']), ['deposit_filter' => 'rejected'])) }}" class="pc-admin-manage-item {{ request('deposit_filter') === 'rejected' ? 'is-active' : '' }}">
                        Rejected Deposits
                        <span class="pc-admin-count">{{ $txCounts['rejected_deposits'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'deposit_filter', 'type', 'status']), ['deposit_filter' => 'initiated'])) }}" class="pc-admin-manage-item {{ request('deposit_filter') === 'initiated' ? 'is-active' : '' }}">
                        Initiated Deposits
                        <span class="pc-admin-count">{{ $txCounts['initiated_deposits'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('admin.transactions.index', array_merge(request()->except(['page', 'transfer_filter', 'withdrawal_filter', 'deposit_filter', 'type', 'status']), ['deposit_filter' => 'all'])) }}" class="pc-admin-manage-item {{ request('deposit_filter') === 'all' ? 'is-active' : '' }}">
                        All Deposits
                        <span class="pc-admin-count">{{ $txCounts['all_deposits'] ?? 0 }}</span>
                    </a>
                </div>
            </aside>
        </div>

        <div class="glass rounded-2xl overflow-hidden pc-admin-list-shell">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Transaction Console</p>
                    <p class="text-xs text-slate-500 mt-1">Monitor transfer flows, review statuses, and flag suspicious activity.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="pc-stat-chip">Transfers: {{ $txCounts['all_transfers'] ?? 0 }}</span>
                    <span class="pc-stat-chip">Pending W/D: {{ $txCounts['pending_withdrawals'] ?? 0 }}</span>
                    <span class="pc-stat-chip">Deposits: {{ $txCounts['all_deposits'] ?? 0 }}</span>
                    <span class="pc-stat-chip">Rejected: {{ ($txCounts['rejected_transfers'] ?? 0) + ($txCounts['rejected_withdrawals'] ?? 0) }}</span>
                </div>
            </div>
        </div>
        <div class="p-4 border-b border-slate-200">
            <form method="GET" class="grid gap-3 items-center">
                <input name="search" type="text" placeholder="Search reference..." value="{{ request('search') }}" />
                <select name="type">
                    <option value="">All Types</option>
                    <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                    <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Flagged</option>
                </select>
                <button type="submit" class="btn-primary text-white px-5 py-2.5 rounded-lg text-sm w-full md:w-auto">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
        <table class="pc-admin-table min-w-[980px]">
            <thead><tr>
                <th>Reference</th><th>Type</th><th>From</th><th>To</th><th>Amount</th><th>Status</th><th>Date</th><th class="text-right">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr class="{{ $tx->status === 'flagged' ? 'bg-red-50/50' : '' }}">
                    <td class="font-mono text-xs text-slate-500">{{ $tx->reference }}</td>
                    <td>
                        <span class="pc-pill {{ $tx->type === 'deposit' ? 'pc-pill-live' : ($tx->type === 'withdrawal' ? 'pc-pill-alert' : 'pc-pill-info') }}">
                            {{ ucfirst($tx->type) }}
                        </span>
                    </td>
                    <td class="text-xs text-slate-600">{{ optional(optional($tx->senderAccount)->user)->name ?? '—' }}</td>
                    <td class="text-xs text-slate-600">{{ optional(optional($tx->receiverAccount)->user)->name ?? '—' }}</td>
                    <td class="font-semibold text-slate-900 text-base">£{{ number_format($tx->amount, 2) }}</td>
                    <td>
                        @php
                            $statusLabel = $tx->status === 'failed' ? 'rejected' : $tx->status;
                        @endphp
                        <span class="pc-pill {{ $tx->status === 'completed' ? 'pc-pill-live' : ($tx->status === 'flagged' ? 'pc-pill-alert' : 'pc-pill-warning') }}">{{ ucfirst($statusLabel) }}</span>
                    </td>
                    <td class="text-xs text-slate-500">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <div class="flex justify-end">
                        @if($tx->status !== 'flagged')
                        <form method="POST" action="{{ route('admin.transactions.flag', $tx) }}">@csrf
                            <button class="pc-action-link pc-action-link-delete">Flag</button>
                        </form>
                        @else
                            <span class="pc-pill pc-pill-alert">Flagged</span>
                        @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-slate-500">No transactions.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="p-4 border-t border-slate-200">{{ $transactions->links() }}</div>
    </div>
</div>
</div>
@endsection
