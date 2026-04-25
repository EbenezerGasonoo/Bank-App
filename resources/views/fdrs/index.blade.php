@extends('layouts.app')
@section('title', 'FDR')
@section('page-title', 'Fixed Deposit Receipt (FDR)')
@section('page-subtitle', 'Open and track fixed deposits')

@section('content')
<div class="space-y-6">
    <div class="grid xl:grid-cols-[320px_minmax(0,1fr)] gap-6 items-start">
        <div class="glass rounded-2xl p-5 border border-slate-200 pc-fdr-form-card">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Open a New FDR</h3>
            <form method="POST" action="{{ route('fdrs.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="pc-fdr-label">Funding Account</label>
                    <select name="account_id" required class="pc-fdr-input">
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ (string) old('account_id') === (string) $account->id ? 'selected' : '' }}>
                            {{ ucfirst($account->type) }} — {{ $account->account_number }} ({{ $account->formatted_balance }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pc-fdr-label">Deposit Amount (£)</label>
                    <input type="number" name="principal" min="500" step="100" value="{{ old('principal', '10000') }}" required class="pc-fdr-input" />
                </div>
                <div>
                    <label class="pc-fdr-label">Tenure</label>
                    <select name="term_months" required class="pc-fdr-input">
                        <option value="3" {{ old('term_months') == '3' ? 'selected' : '' }}>3 months (4.80%)</option>
                        <option value="6" {{ old('term_months') == '6' ? 'selected' : '' }}>6 months (5.20%)</option>
                        <option value="12" {{ old('term_months', '12') == '12' ? 'selected' : '' }}>12 months (5.80%)</option>
                        <option value="24" {{ old('term_months') == '24' ? 'selected' : '' }}>24 months (6.40%)</option>
                    </select>
                </div>
                <div>
                    <label class="pc-fdr-label">Payout Mode</label>
                    <select name="payout_mode" required class="pc-fdr-input">
                        <option value="maturity" {{ old('payout_mode', 'maturity') === 'maturity' ? 'selected' : '' }}>At maturity</option>
                        <option value="monthly" {{ old('payout_mode') === 'monthly' ? 'selected' : '' }}>Monthly interest</option>
                    </select>
                </div>
                <div>
                    <label class="pc-fdr-label">Notes (optional)</label>
                    <input type="text" name="notes" maxlength="150" value="{{ old('notes') }}" placeholder="e.g. School fees reserve" class="pc-fdr-input" />
                </div>
                <button class="btn-primary w-full text-white py-2.5 rounded-lg font-semibold">Open FDR</button>
            </form>
        </div>

        <div class="glass rounded-2xl p-5 border border-slate-200 overflow-hidden pc-statement-shell">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-900">My FDR Portfolio</h3>
                <span class="text-xs text-slate-500">Auto-updated on each placement</span>
            </div>
            <div class="overflow-x-auto">
                <table class="pc-statement-table min-w-[760px]">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Principal</th>
                            <th>Rate</th>
                            <th>Tenure</th>
                            <th>Interest</th>
                            <th>Maturity Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fdrs as $fdr)
                        <tr>
                            <td class="text-xs text-slate-600 font-mono">{{ $fdr->account?->account_number ?? '—' }}</td>
                            <td class="font-semibold text-slate-900">£{{ number_format($fdr->principal, 2) }}</td>
                            <td>{{ number_format($fdr->annual_rate, 2) }}%</td>
                            <td>{{ $fdr->term_months }} months</td>
                            <td>£{{ number_format($fdr->expected_interest, 2) }}</td>
                            <td class="font-semibold text-[#003b70]">£{{ number_format($fdr->maturity_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-10 text-slate-500">No FDR placements yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $fdrs->links() }}</div>
        </div>
    </div>
</div>
@endsection

