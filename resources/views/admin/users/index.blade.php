@extends('layouts.app')
@section('title', 'Users — Admin')
@section('page-title', 'User Management')

@section('content')
<div class="grid lg:grid-cols-[250px_minmax(0,1fr)] gap-6">
    <aside class="pc-admin-manage-panel rounded-2xl overflow-hidden">
        <div class="pc-admin-manage-header">
            <span class="text-sm font-semibold">Manage Account</span>
            <span class="pc-admin-manage-badge">!</span>
        </div>
        <div class="pc-admin-manage-group">
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'profile_state', 'account_status']), ['profile_state' => 'incomplete'])) }}" class="pc-admin-manage-item {{ request('profile_state') === 'incomplete' ? 'is-active' : '' }}">Profile Incomplete</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'profile_state', 'account_status']), ['profile_state' => 'complete'])) }}" class="pc-admin-manage-item {{ request('profile_state') === 'complete' ? 'is-active' : '' }}">Profile Completed</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'profile_state', 'account_status']), ['account_status' => 'active'])) }}" class="pc-admin-manage-item {{ request('account_status') === 'active' ? 'is-active' : '' }}">Active</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'profile_state', 'account_status']), ['account_status' => 'suspended'])) }}" class="pc-admin-manage-item {{ request('account_status') === 'suspended' ? 'is-active' : '' }}">Banned</a>
        </div>
        <div class="pc-admin-manage-group">
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'email_verified']), ['email_verified' => '0'])) }}" class="pc-admin-manage-item {{ request('email_verified') === '0' ? 'is-active' : '' }}">
                Email Unverified
                <span class="pc-admin-count">{{ $userCounts['email_unverified'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'mobile_verified']), ['mobile_verified' => '0'])) }}" class="pc-admin-manage-item {{ request('mobile_verified') === '0' ? 'is-active' : '' }}">
                Mobile Unverified
                <span class="pc-admin-count">{{ $userCounts['mobile_unverified'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'kyc_bucket']), ['kyc_bucket' => 'unverified'])) }}" class="pc-admin-manage-item {{ request('kyc_bucket') === 'unverified' ? 'is-active' : '' }}">
                KYC Unverified
                <span class="pc-admin-count">{{ $userCounts['kyc_unverified'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'email_verified']), ['email_verified' => '1'])) }}" class="pc-admin-manage-item {{ request('email_verified') === '1' ? 'is-active' : '' }}">Email Verified</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'mobile_verified']), ['mobile_verified' => '1'])) }}" class="pc-admin-manage-item {{ request('mobile_verified') === '1' ? 'is-active' : '' }}">Mobile Verified</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'kyc_bucket']), ['kyc_bucket' => 'verified'])) }}" class="pc-admin-manage-item {{ request('kyc_bucket') === 'verified' ? 'is-active' : '' }}">KYC Verified</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except(['page', 'kyc_bucket']), ['kyc_bucket' => 'pending'])) }}" class="pc-admin-manage-item {{ request('kyc_bucket') === 'pending' ? 'is-active' : '' }}">
                KYC Pending
                <span class="pc-admin-count">{{ $userCounts['kyc_pending'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="pc-admin-manage-item {{ request()->query() ? '' : 'is-active' }}">All Accounts</a>
            <button type="button" class="pc-admin-manage-item text-left" disabled>Send Notification</button>
        </div>
    </aside>

    <div class="glass rounded-2xl overflow-hidden pc-admin-list-shell">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Customer Directory</p>
                    <p class="text-xs text-slate-500 mt-1">Search, filter, and manage KYC/account access in one place.</p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="pc-stat-chip">Total: {{ $userCounts['all'] ?? 0 }}</span>
                    <span class="pc-stat-chip">Active: {{ $userCounts['active'] ?? 0 }}</span>
                    <span class="pc-stat-chip">Pending KYC: {{ $userCounts['kyc_pending'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        {{-- Filters --}}
        <div class="p-4 border-b border-slate-200 flex gap-3">
            <form method="GET" class="flex gap-3 w-full">
                <input name="search" type="text" placeholder="Search name or email..." value="{{ request('search') }}" class="flex-1" style="max-width:280px"/>
                <select name="kyc_status" style="width:auto">
                    <option value="">All KYC</option>
                    <option value="pending" {{ request('kyc_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('kyc_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('kyc_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="btn-primary text-white px-5 py-2 rounded-lg text-sm">Filter</button>
            </form>
        </div>
        <table class="pc-admin-table">
            <thead>
            <tr>
                <th>User</th><th>Phone</th><th>KYC</th><th>Status</th><th>Accounts</th><th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="text-slate-900 hover:text-[#056dae] font-medium">{{ $user->name }}</a>
                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                    </td>
                    <td class="text-slate-500 text-xs">{{ $user->phone ?? '—' }}</td>
                    <td>
                        <span class="text-xs px-2 py-1 rounded-full {{ $user->kyc_status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($user->kyc_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ ucfirst($user->kyc_status) }}
                        </span>
                    </td>
                    <td>
                        <span class="text-xs px-2 py-1 rounded-full {{ $user->account_status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($user->account_status) }}
                        </span>
                    </td>
                    <td class="text-slate-500 text-xs">{{ $user->accounts->count() }} account(s)</td>
                    <td>
                        <div class="flex gap-2 flex-wrap justify-end">
                            <a href="{{ route('admin.users.show', $user) }}" class="pc-action-link pc-action-link-edit">View</a>
                            @if($user->kyc_status === 'pending')
                            <form method="POST" action="{{ route('admin.users.approveKyc', $user) }}" class="inline">@csrf
                                <button class="pc-action-link pc-action-link-approve">Approve KYC</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.rejectKyc', $user) }}" class="inline">@csrf
                                <button class="pc-action-link pc-action-link-delete">Reject</button>
                            </form>
                            @endif
                            @if($user->account_status === 'active')
                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">@csrf
                                <button class="pc-action-link pc-action-link-warn">Suspend</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">@csrf
                                <button class="pc-action-link pc-action-link-approve">Activate</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">{{ $users->links() }}</div>
    </div>
</div>
@endsection
