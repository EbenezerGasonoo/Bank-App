@extends('layouts.app')
@section('title', 'Approvals')
@section('page-title', 'Sensitive Action Approvals')
@section('page-subtitle', 'Review customer transfer, deposit, and KYC requests')

@section('content')
<div class="space-y-6">
    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Pending and Historical Requests</h3>
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach([
                'all' => 'All',
                'transfer' => 'Transfer',
                'deposit' => 'Deposit',
                'kyc' => 'KYC',
                'other' => 'Other',
            ] as $key => $label)
                <a href="{{ route('admin.approvals.index', ['category' => $key]) }}"
                   class="px-3 py-1.5 rounded-full text-xs border {{ $category === $key ? 'bg-[#003b70] text-white border-[#003b70]' : 'bg-white text-slate-600 border-slate-300 hover:border-slate-400' }}">
                    {{ $label }} ({{ $tabCounts[$key] ?? 0 }})
                </a>
            @endforeach
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">Action</th>
                        <th class="py-3 pr-4">Target</th>
                        <th class="py-3 pr-4">Requested By</th>
                        <th class="py-3 pr-4">Payload</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3 pr-4">Created</th>
                        <th class="py-3">Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvals as $approval)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="py-3 pr-4 text-slate-900">{{ ucwords(str_replace('_', ' ', $approval->action)) }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ class_basename($approval->target_type) }} #{{ $approval->target_id }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $approval->requester?->name ?? 'System' }}</td>
                        <td class="py-3 pr-4 text-slate-600 text-xs font-mono">{{ json_encode($approval->payload ?? []) }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded-full text-xs {{ $approval->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($approval->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($approval->status) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-slate-500 text-xs">{{ $approval->created_at->diffForHumans() }}</td>
                        <td class="py-3">
                            @if($approval->status === 'pending')
                                <form method="POST" action="{{ route('admin.approvals.approve', $approval) }}" class="mb-2">@csrf
                                    <input name="review_note" type="text" placeholder="Optional note" class="mb-2 text-xs" />
                                    <button class="btn-success px-3 py-1.5 rounded text-xs text-white">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.approvals.reject', $approval) }}">@csrf
                                    <input name="review_note" type="text" placeholder="Optional reason" class="mb-2 text-xs" />
                                    <button class="btn-danger px-3 py-1.5 rounded text-xs text-white">Reject</button>
                                </form>
                            @else
                                <p class="text-xs text-slate-500">By: {{ $approval->approver?->name ?? '—' }}</p>
                                <p class="text-xs text-slate-500">{{ $approval->reviewed_at?->diffForHumans() ?? '—' }}</p>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">No approval requests yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $approvals->links() }}</div>
    </div>
</div>
@endsection

