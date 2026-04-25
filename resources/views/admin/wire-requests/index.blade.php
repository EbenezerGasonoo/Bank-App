@extends('layouts.app')
@section('title', 'Wire Code Requests')
@section('page-title', 'Wire Code Requests')
@section('page-subtitle', 'Generate transfer authentication codes for customers')

@section('content')
<div class="glass rounded-2xl overflow-hidden">
    <table class="pc-admin-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Resolved By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $wireRequest)
            <tr>
                <td>
                    <p class="text-sm text-slate-900 font-semibold">{{ $wireRequest->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $wireRequest->user->email }}</p>
                </td>
                <td class="text-xs text-slate-500">{{ $wireRequest->requested_at?->format('d M Y, H:i') }}</td>
                <td>
                    <span class="pc-pill {{ $wireRequest->status === 'pending' ? 'pc-pill-warning' : 'pc-pill-live' }}">
                        {{ ucfirst($wireRequest->status) }}
                    </span>
                </td>
                <td class="text-xs text-slate-500">{{ $wireRequest->resolver?->name ?? '—' }}</td>
                <td>
                    @if($wireRequest->status === 'pending')
                    <form method="POST" action="{{ route('admin.wire-requests.issue', $wireRequest) }}" class="grid md:grid-cols-5 gap-2 items-end">
                        @csrf
                        <input name="pin" type="text" placeholder="PIN" required />
                        <input name="tax_code" type="text" placeholder="Tax Code" required />
                        <input name="imf_code" type="text" placeholder="IMF Code" required />
                        <input name="cot_code" type="text" placeholder="COT Code" required />
                        <button class="btn-primary text-white text-xs px-3 py-2 rounded">Issue Codes</button>
                    </form>
                    @else
                    <p class="text-xs text-slate-500">Issued {{ $wireRequest->resolved_at?->diffForHumans() }}</p>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-10 text-slate-500">No wire code requests.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-200">{{ $requests->links() }}</div>
</div>
@endsection

