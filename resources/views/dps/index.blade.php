@extends('layouts.app')
@section('title', 'DPS')
@section('page-title', 'DPS')
@section('page-subtitle', 'Deposit Pension Scheme Plans')

@section('content')
<div class="space-y-6">
    <section class="glass rounded-2xl border border-slate-200 p-5 md:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Deposit Pension Scheme</h3>
                <p class="text-sm text-slate-500 mt-1">Build disciplined monthly savings with transparent plan tracking.</p>
            </div>
            <span class="pc-statement-pill is-transfer">DPS Planner</span>
        </div>
    </section>

    <div class="grid md:grid-cols-3 gap-4">
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-kpi">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Total Monthly Commitment</p>
            <p class="text-xl font-semibold text-slate-900 mt-1">£{{ number_format($summary['total_monthly'], 2) }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-kpi">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Pending</p>
            <p class="text-xl font-semibold text-amber-700 mt-1">{{ $summary['pending_count'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-statement-kpi">
            <p class="text-xs text-slate-500 uppercase tracking-wide">Active</p>
            <p class="text-xl font-semibold text-emerald-700 mt-1">{{ $summary['active_count'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[minmax(0,1fr)_380px] gap-6 items-start">
        <div class="glass rounded-2xl border border-slate-200 overflow-hidden pc-statement-shell">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Your DPS Requests</p>
                    <p class="text-xs text-slate-500 mt-0.5">Track submitted plans and review approval progress.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="pc-statement-table min-w-[720px]">
                    <thead>
                        <tr>
                            <th>Requested</th>
                            <th>Plan</th>
                            <th>Monthly Amount</th>
                            <th>Tenure</th>
                            <th>Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dpsRequests as $dps)
                            <tr>
                                <td class="text-xs text-slate-600">{{ $dps->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-sm text-slate-800">{{ $dps->plan_name }}</td>
                                <td class="font-semibold text-slate-900">£{{ number_format((float) $dps->monthly_amount, 2) }}</td>
                                <td class="text-sm text-slate-700">{{ $dps->tenure_months }} months</td>
                                <td class="text-sm text-slate-700">{{ number_format((float) $dps->interest_rate, 2) }}%</td>
                                <td>
                                    <span class="pc-statement-pill {{ $dps->status === 'active' ? 'is-completed' : ($dps->status === 'pending' ? 'is-status' : 'is-withdrawal') }}">
                                        {{ ucfirst($dps->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-500">No DPS requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200">
                {{ $dpsRequests->links() }}
            </div>
        </div>

        <div class="glass rounded-2xl p-5 border border-slate-200 pc-dps-form-card">
            <h3 class="text-sm font-semibold text-slate-900">New DPS Plan Request</h3>
            <p class="text-xs text-slate-500 mt-1">Set your monthly amount and tenure to start saving.</p>

            <form method="POST" action="{{ route('dps.store') }}" class="grid gap-4 mt-4">
                @csrf
                <label>
                    <span class="text-xs text-slate-600">Plan Name</span>
                    <input name="plan_name" type="text" class="mt-1" value="{{ old('plan_name') }}" required />
                    @error('plan_name')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    <span class="text-xs text-slate-600">Monthly Amount (£)</span>
                    <input name="monthly_amount" type="number" step="0.01" min="10" class="mt-1" value="{{ old('monthly_amount') }}" required />
                    @error('monthly_amount')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    <span class="text-xs text-slate-600">Tenure (Months)</span>
                    <input name="tenure_months" type="number" min="6" max="240" class="mt-1" value="{{ old('tenure_months') }}" required />
                    @error('tenure_months')
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

                <button type="submit" class="btn-primary text-white px-4 py-2.5 rounded-lg text-sm">Submit DPS Request</button>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-200">
                <p class="text-xs text-slate-500">Typical processing time: <span class="text-slate-700 font-medium">1-2 business days</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
