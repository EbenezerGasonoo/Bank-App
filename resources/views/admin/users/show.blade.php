@extends('layouts.app')
@section('title', $user->name . ' — Admin')
@section('page-title', $user->name)
@section('page-subtitle', $user->email)

@section('content')
<div class="space-y-6">
    {{-- User Info --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="glass rounded-2xl p-6 space-y-4">
            <h3 class="text-slate-900 font-semibold text-sm">User Details</h3>
            @foreach(['Name' => $user->name, 'Email' => $user->email, 'Phone' => $user->phone ?? '—', 'DOB' => optional($user->date_of_birth)->format('d M Y') ?? '—', 'Address' => $user->address ?? '—', 'KYC Status' => ucfirst($user->kyc_status), 'Account Status' => ucfirst($user->account_status), 'Role' => ucfirst($user->role)] as $label => $val)
            <div class="flex justify-between border-b border-slate-200 pb-3">
                <span class="text-xs text-slate-500">{{ $label }}</span>
                <span class="text-sm text-slate-900">{{ $val }}</span>
            </div>
            @endforeach
            @if($user->id_document_path)
            <a href="{{ Storage::url($user->id_document_path) }}" target="_blank" class="text-xs text-[#056dae] font-medium hover:underline">View ID Document</a>
            @endif
        </div>

        {{-- KYC Actions --}}
        <div class="glass rounded-2xl p-6 space-y-4">
            <h3 class="text-slate-900 font-semibold text-sm">Admin Actions</h3>
            @if($user->kyc_status === 'pending')
            <form method="POST" action="{{ route('admin.users.approveKyc', $user) }}">@csrf
                <button class="btn-success w-full text-white py-3 rounded-lg font-semibold text-sm">✓ Approve KYC &amp; Create Account</button>
            </form>
            <form method="POST" action="{{ route('admin.users.rejectKyc', $user) }}">@csrf
                <button class="btn-danger w-full text-white py-3 rounded-lg font-semibold text-sm">✕ Reject KYC</button>
            </form>
            @endif
            @if($user->account_status === 'active')
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}">@csrf
                <button class="w-full glass text-amber-800 border-amber-200 py-3 rounded-lg font-semibold text-sm bg-amber-50 hover:bg-amber-100/80 transition-all">Suspend Account</button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.users.activate', $user) }}">@csrf
                <button class="w-full glass text-emerald-800 border-emerald-200 py-3 rounded-lg font-semibold text-sm bg-emerald-50 hover:bg-emerald-100/80 transition-all">Activate Account</button>
            </form>
            @endif
            @if(auth()->user()?->isSuperAdmin() && !$user->isAdmin())
            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Delete this user account permanently? This will remove all linked accounts and data. This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button class="w-full text-red-700 py-3 rounded-lg font-semibold text-sm glass border-red-200 border hover:bg-red-50 transition-all">Delete User Account</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Accounts & Credit/Debit --}}
    @foreach($user->accounts as $account)
    <div class="glass rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-slate-900 font-semibold">{{ ucfirst($account->type) }} Account</h3>
                <p class="text-xs text-slate-500 font-mono">{{ $account->account_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-slate-900">£{{ number_format($account->balance, 2) }}</p>
                <span class="text-xs {{ $account->status === 'active' ? 'text-emerald-700' : 'text-red-600' }} font-medium">{{ ucfirst($account->status) }}</span>
            </div>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <form method="POST" action="{{ route('admin.accounts.credit', $account) }}" class="glass rounded-xl p-4 space-y-2">@csrf
                <label class="text-xs text-slate-500">Credit Amount (£)</label>
                <input name="amount" type="number" step="0.01" min="0.01" placeholder="0.00" required />
                <input name="description" type="text" placeholder="Description" />
                <input name="reference" type="text" placeholder="Reference (optional)" maxlength="50" />
                <label class="text-xs text-slate-500">Backdate (optional)</label>
                <input name="backdated_at" type="datetime-local" />
                <button class="btn-success w-full text-white py-2 rounded-lg text-sm font-semibold">+ Credit</button>
                <p class="text-[11px] text-slate-500">Credits under £10,000 post immediately; larger credits require super admin approval.</p>
            </form>
            <form method="POST" action="{{ route('admin.accounts.debit', $account) }}" class="glass rounded-xl p-4 space-y-2">@csrf
                <label class="text-xs text-slate-500">Debit Amount (£)</label>
                <input name="amount" type="number" step="0.01" min="0.01" placeholder="0.00" required />
                <input name="description" type="text" placeholder="Description" />
                <input name="reference" type="text" placeholder="Reference (optional)" maxlength="50" />
                <label class="text-xs text-slate-500">Backdate (optional)</label>
                <input name="backdated_at" type="datetime-local" />
                <button class="btn-danger w-full text-white py-2 rounded-lg text-sm font-semibold">- Debit</button>
                <p class="text-[11px] text-slate-500">Amounts >= £10,000 require approval.</p>
            </form>
            <div class="glass rounded-xl p-4 space-y-2">
                @if($account->status === 'active')
                <form method="POST" action="{{ route('admin.accounts.freeze', $account) }}">@csrf
                    <input name="description" type="text" placeholder="Reason (optional)" />
                    <button class="w-full text-[#056dae] py-2 rounded-lg text-sm font-semibold glass border-slate-200 border hover:border-[#056dae] transition-all">❄ Freeze Account</button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.accounts.unfreeze', $account) }}">@csrf
                    <input name="description" type="text" placeholder="Reason (optional)" />
                    <button class="btn-success w-full text-white py-2 rounded-lg text-sm font-semibold">✓ Unfreeze Account</button>
                </form>
                @endif
                @if(auth()->user()?->isSuperAdmin())
                <form method="POST" action="{{ route('admin.accounts.delete', $account) }}" onsubmit="return confirm('Delete this account permanently? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button class="w-full text-red-700 py-2 rounded-lg text-sm font-semibold glass border-red-200 border hover:bg-red-50 transition-all">🗑 Delete Account</button>
                </form>
                <p class="text-[11px] text-slate-500">Only accounts with zero balance and no transaction/ledger history can be deleted.</p>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
