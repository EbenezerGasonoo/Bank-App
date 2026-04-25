<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Poise Commerce Bank') }}</title>
    <meta name="description" content="Poise Commerce Bank — Banking made easy, secure, and personal." />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .theme-poise-retail.welcome-hero-bank { min-height: 100vh; }
    </style>
</head>
@php
    $homeHero = config('poise_media.home_hero');
    $acc = config('poise_media.section_accents', []);
    $homeTileImages = array_slice($acc, 0, 3);
    $fallbackImage = asset('images/bank-placeholder.svg');
    $heroFallback = asset('images/hero-fallback.svg');
@endphp
<body class="theme-poise-retail antialiased relative">
    <div class="pc-bank-page-bg" aria-hidden="true">
        <div class="pc-bank-orb pc-bank-orb-1"></div>
        <div class="pc-bank-orb pc-bank-orb-2"></div>
        <div class="pc-bank-orb pc-bank-orb-3"></div>
    </div>
    @include('partials.public-top-bar', ['current' => null])
    @include('partials.public-nav', ['current' => null])

    {{-- Hero: placement modeled after large retail-bank homepages --}}
    <section class="relative z-10 pt-28 pb-10 welcome-hero-bank">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-12 gap-6 items-stretch">
                <div class="lg:col-span-8">
                    <div class="pc-hero-image-panel relative rounded-3xl overflow-hidden shadow-2xl isolate">
                        <img
                            src="{{ $homeHero }}"
                            alt="Modern architecture and business district"
                            class="relative z-0 w-full h-[470px] object-cover block"
                            width="1200"
                            height="470"
                            loading="eager"
                            onerror="this.onerror=null;this.src='{{ $heroFallback }}';"
                        />
                        <div class="pc-hero-image-overlay pointer-events-none absolute inset-0 z-10" aria-hidden="true"></div>
                        <div class="absolute inset-0 z-20 flex items-end">
                            <div class="p-7 lg:p-10 max-w-2xl">
                                <div class="reveal pc-hero-kicker inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs mb-5 tracking-widest uppercase font-semibold bg-white/10 border border-white/40 text-white">
                                    Personal Banking
                                </div>
                                <h1 class="reveal reveal-stagger-1 text-4xl lg:text-6xl font-bold text-white leading-tight mb-5 pc-font-display">
                                    Bank smarter with award-winning digital tools.
                                </h1>
                                <p class="reveal reveal-stagger-2 text-base lg:text-lg text-white/90 leading-relaxed mb-6">
                                    Open an account in minutes, move money quickly, and manage your cards and security from one place.
                                </p>
                                <div class="reveal reveal-stagger-3 flex flex-wrap gap-3">
                                    <a href="{{ route('register') }}" class="btn-primary px-7 py-3.5 rounded-lg font-semibold text-base">Open an account</a>
                                    <a href="{{ route('login') }}" class="pc-hero-secondary px-7 py-3.5 rounded-lg font-semibold text-base">Sign in</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-4">
                    <div class="glass rounded-2xl p-5">
                        <p class="text-xs text-slate-500 mb-3 uppercase tracking-wider">Sign on</p>
                        <div class="space-y-3">
                            <input type="text" disabled value="User ID" class="w-full opacity-80 cursor-not-allowed rounded-md border border-slate-200 bg-slate-50 text-slate-600 px-3 py-2 text-sm" />
                            <input type="password" disabled value="********" class="w-full opacity-80 cursor-not-allowed rounded-md border border-slate-200 bg-slate-50 text-slate-600 px-3 py-2 text-sm" />
                            <div class="text-xs text-slate-500 flex items-center gap-2">
                                <input type="checkbox" checked disabled class="rounded border-slate-300" />
                                Remember User ID
                            </div>
                            <button type="button" class="btn-primary w-full py-3 rounded-lg font-semibold text-white">Sign on</button>
                            <div class="text-xs text-slate-500 flex justify-between">
                                <span>Register</span><span>Forgot User ID or Password?</span>
                            </div>
                        </div>
                    </div>

                    <div class="pc-hero-band rounded-2xl p-5">
                        <p class="text-xs uppercase tracking-wider text-white/70 mb-3">At a glance</p>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([['28+','Years'],['£50B+','Volume'],['24/7','Support']] as $si => $stat)
                            <div class="pc-stat-tile rounded-lg p-3 text-center">
                                <div class="text-lg font-bold text-white">{!! $stat[0] !!}</div>
                                <div class="text-[10px] text-white/80 mt-1">{{ $stat[1] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @if($announcements->count())
                    <div class="glass rounded-2xl p-5">
                        <p class="text-xs text-slate-500 mb-3 uppercase tracking-wider">Latest from the bank</p>
                        @foreach($announcements->take(2) as $ann)
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $ann->type === 'alert' ? 'bg-red-500' : ($ann->type === 'warning' ? 'bg-amber-400' : 'bg-[#056dae]') }}"></div>
                            <div>
                                <p class="text-sm text-[#1a1f26] font-medium">{{ $ann->title }}</p>
                                <p class="text-xs text-slate-500">{{ Str::limit($ann->body, 60) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-4 mt-6">
                @foreach([
                    ['Checking', route('public.personal')],
                    ['Savings', route('public.savings')],
                    ['Credit cards', route('public.products_cards')],
                    ['Security center', route('public.security_center')],
                ] as [$label, $url])
                <a href="{{ $url }}" class="reveal glass rounded-xl px-5 py-4 text-sm font-medium text-[#003b70] hover:text-[#056dae] hover:shadow-md transition-all">
                    {{ $label }} →
                </a>
                @endforeach
            </div>
        </div>
    </section>
    <section class="relative z-10 pb-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-6 mb-6">
                <a href="{{ route('public.personal') }}" class="reveal group glass rounded-2xl overflow-hidden block hover:shadow-md transition-all hover:-translate-y-0.5 border-slate-200/90">
                    <img src="{{ $homeTileImages[0] ?? $homeHero }}" alt="" class="tile-image-top group-hover:scale-105 transition-transform duration-500" loading="lazy" width="400" height="200" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                    <div class="p-6">
                        <p class="text-[#003b70] font-semibold">Checking accounts</p>
                        <p class="text-sm text-slate-500 mt-2">Choose daily banking with debit access and digital controls.</p>
                    </div>
                </a>
                <a href="{{ route('public.savings') }}" class="reveal reveal-stagger-1 group glass rounded-2xl overflow-hidden block hover:shadow-md transition-all hover:-translate-y-0.5 border-slate-200/90">
                    <img src="{{ $homeTileImages[1] ?? $homeHero }}" alt="" class="tile-image-top group-hover:scale-105 transition-transform duration-500" loading="lazy" width="400" height="200" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                    <div class="p-6">
                        <p class="text-[#003b70] font-semibold">Savings &amp; CDs</p>
                        <p class="text-sm text-slate-500 mt-2">Build savings goals with fixed and flexible options.</p>
                    </div>
                </a>
                <a href="{{ route('public.security_center') }}" class="reveal reveal-stagger-2 group glass rounded-2xl overflow-hidden block hover:shadow-md transition-all hover:-translate-y-0.5 border-slate-200/90">
                    <img src="{{ $homeTileImages[2] ?? $homeHero }}" alt="" class="tile-image-top group-hover:scale-105 transition-transform duration-500" loading="lazy" width="400" height="200" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                    <div class="p-6">
                        <p class="text-[#003b70] font-semibold">Security center</p>
                        <p class="text-sm text-slate-500 mt-2">Monitor fraud alerts and learn common scam defenses.</p>
                    </div>
                </a>
            </div>
            <div class="glass rounded-2xl p-8">
                <p class="text-xs text-slate-500 mb-4 uppercase tracking-wider">Common questions</p>
                <div class="grid md:grid-cols-3 gap-6 text-sm text-slate-600">
                    <div>
                        <p class="text-[#1a1f26] font-medium mb-2">How do transfers work?</p>
                        <p>Email OTP protects transfers to other account numbers before funds are sent.</p>
                    </div>
                    <div>
                        <p class="text-[#1a1f26] font-medium mb-2">How do I get access?</p>
                        <p>Use Open an account to register, then sign in to your dashboard.</p>
                    </div>
                    <div>
                        <p class="text-[#1a1f26] font-medium mb-2">Need help?</p>
                        <p>Visit <a href="{{ route('public.support') }}" class="text-[#056dae] hover:underline font-medium">Support</a>, <a href="{{ route('public.security_center') }}" class="text-[#056dae] hover:underline font-medium">Security</a>, or <a href="{{ route('public.faq') }}" class="text-[#056dae] hover:underline font-medium">FAQ</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
