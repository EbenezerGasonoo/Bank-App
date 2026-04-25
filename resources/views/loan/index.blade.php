@extends('layouts.app')
@section('title', 'Loan')
@section('page-title', 'Loan')
@section('page-subtitle', 'Apply and track your loan requests')

@section('content')
<div class="space-y-6">
    <div class="grid md:grid-cols-3 gap-4">
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Total Requested</p>
            <p class="text-xl font-semibold text-slate-900 mt-1">£{{ number_format($summary['total_requested'], 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Pending</p>
            <p class="text-xl font-semibold text-amber-700 mt-1">{{ $summary['pending_count'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Approved</p>
            <p class="text-xl font-semibold text-emerald-700 mt-1">{{ $summary['approved_count'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">
        <div class="glass rounded-2xl border border-slate-200 overflow-hidden pc-statement-shell">
            <div class="overflow-x-auto">
                <table class="pc-statement-table min-w-[700px]">
                    <thead>
                        <tr>
                            <th>Requested</th>
                            <th>Amount</th>
                            <th>Duration</th>
                            <th>Rate</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loanRequests as $loan)
                            <tr>
                                <td class="text-xs text-slate-600">{{ $loan->created_at->format('d M Y, H:i') }}</td>
                                <td class="font-semibold text-slate-900">£{{ number_format((float) $loan->amount, 2) }}</td>
                                <td class="text-sm text-slate-700">{{ $loan->duration_months }} months</td>
                                <td class="text-sm text-slate-700">{{ number_format((float) $loan->interest_rate, 2) }}%</td>
                                <td class="text-sm text-slate-700">{{ $loan->purpose }}</td>
                                <td>
                                    <span class="pc-statement-pill {{ $loan->status === 'approved' ? 'is-completed' : ($loan->status === 'pending' ? 'is-status' : 'is-withdrawal') }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-500">No loan requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $loanRequests->links() }}
            </div>
        </div>

        <div class="glass rounded-2xl p-5 border border-slate-200">
            <h3 class="text-sm font-semibold text-slate-900">New Loan Request</h3>
            <p class="text-xs text-slate-500 mt-1">Submit a request and our team will review it shortly.</p>

            <form method="POST" action="{{ route('loan.store') }}" class="grid gap-4 mt-4">
                @csrf
                <label>
                    <span class="text-xs text-slate-600">Amount (£)</span>
                    <input name="amount" type="number" step="0.01" min="500" class="mt-1" value="{{ old('amount') }}" required />
                    @error('amount')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </label>
                <label>
                    <span class="text-xs text-slate-600">Duration (Months)</span>
                    <input name="duration_months" type="number" min="3" max="120" class="mt-1" value="{{ old('duration_months') }}" required />
                    @error('duration_months')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </label>
                <label>
                    <span class="text-xs text-slate-600">Purpose</span>
                    <input name="purpose" type="text" class="mt-1" value="{{ old('purpose') }}" required />
                    @error('purpose')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </label>
                <label>
                    <span class="text-xs text-slate-600">Notes (Optional)</span>
                    <textarea name="notes" rows="4" class="mt-1">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="btn-primary text-white px-4 py-2.5 rounded-lg text-sm">Submit Loan Request</button>
            </form>
        </div>
    </div>
</div>
@endsection
