@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Panel')
@section('page-subtitle', 'System Overview')

@section('content')
<div class="space-y-8">
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-7 gap-4">
        @foreach([
            ['Total Users', $totalUsers, 'text-[#056dae]'],
            ['Pending KYC', $pendingKyc, 'text-amber-700'],
            ['Pending Approvals', $pendingApprovals, 'text-rose-700'],
            ['Wire Code Requests', $pendingWireCodeRequests, 'text-blue-700'],
            ['Accounts', $totalAccounts, 'text-indigo-700'],
            ['Transactions', $totalTransactions, 'text-violet-700'],
            ['Total Deposited', '£' . number_format($totalDeposited, 0), 'text-emerald-700'],
        ] as [$label, $val, $color])
        <div class="glass rounded-2xl p-5">
            <p class="text-xs text-slate-500">{{ $label }}</p>
            <p class="text-2xl font-bold {{ $color }} mt-1">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-semibold text-slate-900 text-sm">Recent Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-[#056dae] font-medium hover:underline">View all →</a>
            </div>
            @foreach($recentUsers as $user)
            <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <a href="{{ route('admin.users.show', $user) }}" class="text-sm text-slate-900 hover:text-[#056dae] font-medium">{{ $user->name }}</a>
                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $user->kyc_status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($user->kyc_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ ucfirst($user->kyc_status) }}
                </span>
            </div>
            @endforeach
        </div>

        {{-- Recent Transactions --}}
        <div class="glass rounded-2xl p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-semibold text-slate-900 text-sm">Recent Transactions</h3>
                <a href="{{ route('admin.transactions.index') }}" class="text-xs text-[#056dae] font-medium hover:underline">View all →</a>
            </div>
            @foreach($recentTransactions as $tx)
            <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-0">
                <div>
                    <p class="text-xs font-mono text-slate-500">{{ $tx->reference }}</p>
                    <p class="text-xs text-slate-600">{{ $tx->type }} · {{ $tx->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900">£{{ number_format($tx->amount, 2) }}</p>
                    <span class="text-xs {{ $tx->status === 'flagged' ? 'text-red-600' : 'text-slate-500' }} font-medium">{{ ucfirst($tx->status) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach([
            ['Manage Users', route('admin.users.index'), '👤'],
            ['Transactions', route('admin.transactions.index'), '📊'],
            ['Approvals', route('admin.approvals.index'), '✅'],
            ['Wire Requests', route('admin.wire-requests.index'), '🔐'],
            ['Login Activity', route('admin.security.loginActivity'), '🛡️'],
            ['Announcements', route('admin.announcements.index'), '📢'],
            ['System Management', route('admin.system.index'), '⚙️'],
        ] as [$label, $url, $icon])
        <a href="{{ $url }}" class="glass rounded-2xl p-5 hover:bg-slate-50/80 transition-all text-center border-slate-200/90">
            <div class="text-2xl mb-2">{{ $icon }}</div>
            <p class="text-sm text-slate-900 font-medium">{{ $label }}</p>
        </a>
        @endforeach
    </div>
</div>
@endsection
