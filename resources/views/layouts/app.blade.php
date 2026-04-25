<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Poise Commerce Bank') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <livewire:styles />
    <style>
        .btn-danger { background: linear-gradient(135deg, #c2413a, #9f2f29); }
        .btn-success { background: linear-gradient(135deg, #2f8f62, #226c4a); }
        .glass-hover:hover { background: rgba(0, 0, 0, 0.04); }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; }
        .utility-link { text-decoration: none; }
        label { display: block; font-size: 0.875rem; margin-bottom: 0.5rem; }
        table { width: 100%; border-collapse: collapse; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>
<body class="theme-bank-app antialiased h-screen flex overflow-hidden">
    <aside class="pcbank-sidebar w-64 flex-shrink-0 flex flex-col border-r">
        <div class="p-5 border-b border-white/10">
            <a href="{{ route('home') }}" class="pc-logo-badge block px-3 py-2">
                <img
                    src="{{ asset('images/poise-logo.png') }}"
                    alt="Poise Commerce Bank"
                    class="pc-logo-enhanced h-16 w-auto object-contain"
                    loading="eager"
                    decoding="async"
                />
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            @if(!auth()->user()->isAdmin())
            <a href="{{ route('transactions.index', ['type' => 'deposit']) }}" class="sidebar-link {{ request()->routeIs('transactions.index') && request('type') === 'deposit' ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
                Deposit
            </a>
            <a href="{{ route('transactions.cryptoWithdrawal') }}" class="sidebar-link {{ request()->routeIs('transactions.cryptoWithdrawal') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m4-12H9.5a2.5 2.5 0 000 5H14a2.5 2.5 0 010 5H8"/></svg>
                Withdraw
            </a>
            <a href="{{ route('fdrs.index') }}" class="sidebar-link {{ request()->routeIs('fdrs.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.314 0-6 1.343-6 3s2.686 3 6 3 6 1.343 6 3-2.686 3-6 3m0-12V4m0 16v-3"/></svg>
                FDR
            </a>
            <a href="{{ route('dps.index') }}" class="sidebar-link {{ request()->routeIs('dps.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11l2-2 2 2 4-4m4 5a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                DPS
            </a>
            <a href="{{ route('loan.index') }}" class="sidebar-link {{ request()->routeIs('loan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.5 0-4.5 2-4.5 4.5S9.5 17 12 17s4.5-2 4.5-4.5S14.5 8 12 8zm0 0V5m0 12v2m7-7h2M3 12h2"/></svg>
                Loan
            </a>
            <a href="{{ route('features.show', 'mobile-top-up') }}" class="sidebar-link {{ request()->routeIs('features.show') && request()->route('feature') === 'mobile-top-up' ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 4h10a1 1 0 011 1v14a1 1 0 01-1 1H7a1 1 0 01-1-1V5a1 1 0 011-1z"/></svg>
                Mobile Top Up
            </a>
            <a href="{{ route('transactions.transfer') }}" class="sidebar-link {{ request()->routeIs('transactions.transfer', 'transactions.verifyTransfer', 'transactions.doVerifyTransfer') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Transfer
            </a>
            <a href="{{ route('cards.index') }}" class="sidebar-link {{ request()->routeIs('cards.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Virtual Cards
            </a>
            <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.transfer', 'transactions.verifyTransfer', 'transactions.doVerifyTransfer', 'transactions.cryptoWithdrawal') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m14.836 2A8.003 8.003 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-14.837-2m14.837 2H15"/></svg>
                Transactions
            </a>
            <a href="{{ route('statement.index') }}" class="sidebar-link {{ request()->routeIs('statement.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                Statement
            </a>
            @else
            @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-2 px-4"><span class="text-xs text-sidebar-muted uppercase tracking-widest">Admin</span></div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Admin Panel
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a3 3 0 00-3-3H10a3 3 0 00-3 3v2m10 0H7m8-12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Manage Accounts
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 5h8m-8 5h4M4 5h16v14H4z"/></svg>
                Transfer Console
            </a>
            <a href="{{ route('admin.approvals.index') }}" class="sidebar-link {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Approvals
            </a>
            <a href="{{ route('admin.security.auditLogs') }}" class="sidebar-link {{ request()->routeIs('admin.security.auditLogs') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4V7M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Audit Logs
            </a>
            <a href="{{ route('admin.security.loginActivity') }}" class="sidebar-link {{ request()->routeIs('admin.security.loginActivity') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-3.866 3.582-7 8-7v14c-4.418 0-8-3.134-8-7zm0 0c0-3.866-3.582-7-8-7v14c4.418 0 8-3.134 8-7z"/></svg>
                Login Activity
            </a>
            <a href="{{ route('admin.wire-requests.index') }}" class="sidebar-link {{ request()->routeIs('admin.wire-requests.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Wire Code Requests
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882A1 1 0 0112 5h8a1 1 0 011 1v8a1 1 0 01-.293.707l-5 5A1 1 0 0115 20h-3a1 1 0 01-1-1V5.882zM7 9a4 4 0 100 8 4 4 0 000-8z"/></svg>
                Announcement CMS
            </a>
            <a href="{{ route('admin.system.index') }}" class="sidebar-link {{ request()->routeIs('admin.system.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3a1.5 1.5 0 011.5 1.5v.568a7.5 7.5 0 012.094.868l.402-.402a1.5 1.5 0 012.121 0l.708.707a1.5 1.5 0 010 2.122l-.402.401c.365.662.656 1.36.868 2.095h.568a1.5 1.5 0 011.5 1.5v1a1.5 1.5 0 01-1.5 1.5h-.568a7.5 7.5 0 01-.868 2.094l.402.402a1.5 1.5 0 010 2.121l-.707.708a1.5 1.5 0 01-2.122 0l-.401-.402a7.5 7.5 0 01-2.095.868v.568a1.5 1.5 0 01-1.5 1.5h-1a1.5 1.5 0 01-1.5-1.5v-.568a7.5 7.5 0 01-2.094-.868l-.402.402a1.5 1.5 0 01-2.121 0l-.708-.707a1.5 1.5 0 010-2.122l.402-.401a7.5 7.5 0 01-.868-2.095H3.75a1.5 1.5 0 01-1.5-1.5v-1a1.5 1.5 0 011.5-1.5h.568a7.5 7.5 0 01.868-2.094l-.402-.402a1.5 1.5 0 010-2.121l.707-.708a1.5 1.5 0 012.122 0l.401.402a7.5 7.5 0 012.095-.868V4.5A1.5 1.5 0 019.75 3zM10.5 9.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"/></svg>
                System Management
            </a>
            @endif
            @endif
        </nav>
        <div class="p-4 border-t border-white/10">
            <div class="mb-4 text-xs text-sidebar-muted space-x-3">
                <a href="{{ route('public.support') }}" class="text-sidebar-muted hover:text-white">Support</a>
                <a href="{{ route('public.security_center') }}" class="text-sidebar-muted hover:text-white">Security</a>
                <a href="{{ route('public.faq') }}" class="text-sidebar-muted hover:text-white">FAQ</a>
            </div>
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 mb-3 rounded-lg px-1.5 py-1.5 hover:bg-white/10 transition-colors">
                <div class="w-8 h-8 rounded-full border border-white/20 bg-white/10 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm text-sidebar-bright font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-sidebar-muted capitalize">{{ auth()->user()->role }}</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-xs text-sidebar-muted hover:text-white transition-colors py-1">Sign out →</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <div class="pcbank-utility px-8 h-9 border-b flex items-center justify-between text-xs">
            <div class="space-x-4">
                <a href="{{ route('public.personal') }}" class="utility-link">Personal</a>
                <a href="{{ route('public.business') }}" class="utility-link">Business</a>
                <a href="{{ route('public.commercial') }}" class="utility-link">Commercial</a>
            </div>
            <div class="space-x-4">
                <a href="{{ route('public.customer_service') }}" class="utility-link">Customer service</a>
                <a href="{{ route('public.security_center') }}" class="utility-link">Security Center</a>
                <a href="{{ route('public.atms_and_branches') }}" class="utility-link">Find ATM or branch</a>
            </div>
        </div>
        <div class="pcbank-sticky sticky top-0 z-10 px-8 py-4 flex items-center justify-between app-headline">
            <div>
                <h1 class="text-lg font-semibold app-headline" style="color: #1a1f26;">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-sticky-muted">@yield('page-subtitle', now()->format('l, d F Y'))</p>
            </div>
            <div class="flex items-center gap-3">
                @if(session('success'))
                    <div class="text-sm text-emerald-700 border border-emerald-200 glass px-4 py-2 rounded-lg bg-emerald-50/90">✓ {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="text-sm text-red-700 border border-red-200 glass px-4 py-2 rounded-lg bg-red-50/90">✕ {{ $errors->first() }}</div>
                @endif
                @yield('header-actions')
            </div>
        </div>
        <div class="p-8">
            @yield('content')
        </div>
    </main>
    <livewire:scripts />
</body>
</html>
