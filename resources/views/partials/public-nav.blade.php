@php
    $c = $current ?? '';
@endphp
<nav
    class="theme-pc-mainnav fixed top-9 w-full z-40 border-b-4 border-[#c41230] shadow-sm"
    style="background: #ffffff; backdrop-filter: none;"
>
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-20">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group pc-logo-badge px-2 py-1">
            <img
                src="{{ asset('images/poise-logo.png') }}"
                alt="Poise Commerce Bank"
                class="pc-logo-enhanced h-14 md:h-16 w-auto object-contain"
                loading="eager"
                decoding="async"
            />
        </a>
        <div class="hidden md:flex items-center gap-5 text-sm text-slate-600">
            <a href="{{ route('public.personal') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'personal' ? 'is-active nav-pc-active' : '' }}">Personal</a>
            <a href="{{ route('public.business') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'business' ? 'is-active nav-pc-active' : '' }}">Business</a>
            <a href="{{ route('public.products_cards') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'products_cards' ? 'is-active nav-pc-active' : '' }}">Cards</a>
            <a href="{{ route('public.loans') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'loans' ? 'is-active nav-pc-active' : '' }}">Loans</a>
            <a href="{{ route('public.wealth') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'wealth' ? 'is-active nav-pc-active' : '' }}">Wealth</a>
            <a href="{{ route('public.fdr') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'fdr' ? 'is-active nav-pc-active' : '' }}">FDR</a>
            <a href="{{ route('public.international') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'international' ? 'is-active nav-pc-active' : '' }}">International</a>
            <a href="{{ route('public.support') }}" class="nav-link-animated hover:text-[#003b70] {{ $c === 'support' ? 'is-active nav-pc-active' : '' }}">Support</a>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm text-[#056dae] font-medium hover:underline px-2 py-2">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-sm text-slate-500 hover:text-[#003b70] px-2 py-2">Sign out</button></form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-[#056dae] font-medium px-3 py-2">Sign in</a>
                <a href="{{ route('register') }}" class="btn-primary text-sm text-white px-4 py-2.5 rounded-lg font-semibold">Sign up</a>
            @endauth
        </div>
    </div>
</nav>
