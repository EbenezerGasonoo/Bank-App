@extends('layouts.app')
@section('title', 'Crypto Wallet Withdrawal')
@section('page-title', 'Crypto Wallet Withdrawal')
@section('page-subtitle', 'Feature status update')

@section('content')
<div class="max-w-3xl">
    <div class="glass rounded-2xl p-8 border border-amber-200 bg-amber-50/70">
        <p class="text-xs uppercase tracking-wider text-amber-700 font-semibold mb-3">Coming soon</p>
        <h2 class="text-2xl font-semibold text-slate-900 mb-3">Crypto wallet withdrawal is currently under review</h2>
        <p class="text-sm text-slate-700 leading-6">
            This service is being reviewed by compliance and risk teams before launch.
            You will be notified once crypto wallet withdrawals are enabled for your account.
        </p>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('transactions.transfer') }}" class="btn-primary text-white px-5 py-2 rounded-lg text-sm font-semibold">Back to Transfer</a>
            <a href="{{ route('public.support') }}" class="glass border border-slate-200 text-slate-700 px-5 py-2 rounded-lg text-sm font-semibold">Contact Support</a>
        </div>
    </div>
</div>
@endsection

