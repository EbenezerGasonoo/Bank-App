@extends('layouts.app')
@section('title', 'Credit Score')
@section('page-title', 'Credit Score')
@section('page-subtitle', 'Your current customer score and improvement factors')

@section('content')
<div class="space-y-6">
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-6 lg:col-span-2">
            <p class="text-xs uppercase tracking-wider text-slate-500">Current Score</p>
            <div class="flex items-end gap-4 mt-2">
                <p class="text-5xl font-bold text-[#003b70] pc-font-display">{{ $score }}</p>
                <span class="text-sm px-3 py-1 rounded-full {{ $score >= 660 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $band }}</span>
            </div>
            <div class="mt-4 h-3 rounded-full bg-slate-200 overflow-hidden">
                @php($fill = max(0, min(100, (($score - 300) / 550) * 100)))
                <div class="h-3 rounded-full bg-gradient-to-r from-[#056dae] to-[#003b70]" style="width: {{ $fill }}%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-3">Range: 300 - 850. Higher score can improve eligibility for selected products.</p>
        </div>
        <div class="glass rounded-2xl p-6">
            <p class="text-xs uppercase tracking-wider text-slate-500 mb-3">Activity Snapshot</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Total transactions</span><span class="font-semibold">{{ $totalTx }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Completed</span><span class="font-semibold">{{ $completedTx }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Risk events</span><span class="font-semibold">{{ $riskTx }}</span></div>
            </div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Scoring Factors</h3>
        <div class="grid md:grid-cols-2 gap-3">
            @foreach($factors as $factor)
                <div class="rounded-xl border px-4 py-3 {{ $factor['state'] === 'positive' ? 'border-emerald-200 bg-emerald-50/70 text-emerald-800' : 'border-amber-200 bg-amber-50/70 text-amber-800' }}">
                    <p class="text-sm font-medium">{{ $factor['label'] }}</p>
                    <p class="text-xs mt-1">{{ $factor['state'] === 'positive' ? 'Contributing positively' : 'Needs attention' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">How to improve</h3>
        <ul class="space-y-2 text-sm text-slate-600">
            <li>- Keep your KYC profile approved and information up to date.</li>
            <li>- Avoid failed or flagged payment attempts.</li>
            <li>- Maintain consistent successful transaction activity.</li>
            <li>- Keep your account and security verification details active.</li>
        </ul>
    </div>
</div>
@endsection

