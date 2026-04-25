@extends('layouts.public')

@section('title', $title)
@section('page_title', $title)

@section('content')
@php($fallbackImage = asset('images/bank-placeholder.svg'))
<div class="relative z-10 max-w-6xl mx-auto text-[#1a1f26]">
    <section class="pc-personal-hero rounded-3xl overflow-hidden mb-10 md:mb-12">
        <div class="grid lg:grid-cols-12 gap-0">
            <div class="lg:col-span-7 p-7 md:p-9 lg:p-10">
                <p class="reveal text-xs uppercase tracking-widest text-white/80 mb-4">Personal Banking</p>
                <h1 class="reveal reveal-stagger-1 text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight pc-font-display">{{ $headline }}</h1>
                <p class="reveal reveal-stagger-2 mt-5 text-base md:text-lg text-white/90 leading-relaxed max-w-2xl">{{ $intro }}</p>
                <div class="reveal reveal-stagger-3 mt-7 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="btn-primary inline-block text-white px-6 py-3 rounded-lg font-semibold">Open personal account</a>
                    <a href="{{ route('login') }}" class="pc-personal-hero-link inline-block text-white px-6 py-3 rounded-lg font-semibold">Sign on</a>
                </div>
            </div>
            <div class="lg:col-span-5 relative min-h-[240px]">
                <img
                    src="{{ $heroImage }}"
                    alt="Personal banking with Poise Commerce Bank"
                    class="w-full h-full object-cover"
                    width="900"
                    height="700"
                    loading="eager"
                    decoding="async"
                    onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#002b54]/30 via-transparent to-transparent pointer-events-none"></div>
            </div>
        </div>
    </section>

    <section class="mb-10 md:mb-12">
        <div class="grid md:grid-cols-3 gap-4 md:gap-5">
            @foreach([
                ['Everyday checking', 'Simple day-to-day account with card controls and real-time payments.', route('register'), 'Open checking'],
                ['Savings and goals', 'Build emergency funds and named goals with clear product terms.', route('public.savings'), 'View savings'],
                ['Cards and security', 'Manage debit and virtual card controls with OTP-backed transfers.', route('public.products_cards'), 'Explore cards'],
            ] as [$name, $desc, $url, $cta])
            <article class="reveal pc-personal-card rounded-2xl p-5 md:p-6">
                <h2 class="text-lg font-semibold text-[#003b70] pc-font-display">{{ $name }}</h2>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
                <a href="{{ $url }}" class="mt-5 inline-block text-sm font-semibold text-[#056dae] hover:underline">{{ $cta }} →</a>
            </article>
            @endforeach
        </div>
    </section>

    <section class="mb-10 md:mb-12">
        <div class="grid lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 pc-personal-card rounded-2xl p-6 md:p-7">
                <h2 class="text-xl md:text-2xl font-semibold text-[#003b70] pc-font-display">Why customers choose Poise personal banking</h2>
                <div class="mt-5 grid sm:grid-cols-2 gap-4">
                    @foreach([
                        ['Transparent charges', 'See fees before confirmation and in clear statement descriptions.'],
                        ['Fast digital servicing', 'Move money, view balances, and manage payees without branch-only steps.'],
                        ['Built-in transfer checks', 'Email OTP verification adds a deliberate check on outgoing transfers.'],
                        ['Human support when needed', 'Escalate urgent issues to trained specialists for context-based help.'],
                    ] as [$label, $line])
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
                        <p class="text-sm font-semibold text-[#1a1f26]">{{ $label }}</p>
                        <p class="mt-1.5 text-sm text-slate-600">{{ $line }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <aside class="lg:col-span-4 pc-personal-card rounded-2xl p-6">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-3">Quick start</p>
                <ol class="space-y-3 text-sm text-slate-600">
                    <li><span class="font-semibold text-[#003b70]">1.</span> Create your profile and verify identity.</li>
                    <li><span class="font-semibold text-[#003b70]">2.</span> Open a personal account in minutes.</li>
                    <li><span class="font-semibold text-[#003b70]">3.</span> Add payees and fund your account securely.</li>
                    <li><span class="font-semibold text-[#003b70]">4.</span> Use dashboard tools to monitor spending.</li>
                </ol>
                <a href="{{ route('register') }}" class="btn-primary mt-5 inline-block w-full text-center text-white px-4 py-3 rounded-lg font-semibold">Get started now</a>
            </aside>
        </div>
    </section>

    <section class="space-y-12 md:space-y-14 mb-10 md:mb-12">
        @foreach($sections as $index => $section)
            <div class="grid md:grid-cols-2 gap-6 md:gap-8 items-center">
                @if($index % 2 === 0)
                    <div class="order-1">
                        <h3 class="reveal text-xl font-semibold text-[#003b70] pc-font-display">{{ $section['heading'] }}</h3>
                        <div class="mt-3 space-y-3 text-sm md:text-base text-slate-600 leading-relaxed">
                            @foreach($section['paragraphs'] as $p)
                                <p class="reveal reveal-child">{!! nl2br(e($p)) !!}</p>
                            @endforeach
                        </div>
                        @if(!empty($section['list']))
                            <ul class="mt-4 space-y-2 text-sm md:text-base text-slate-600">
                                @foreach($section['list'] as $item)
                                    <li class="reveal reveal-child flex items-start gap-2.5"><span class="text-[#056dae] mt-0.5">✓</span><span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        @endif
                        @if(!empty($section['callout']))
                            <div class="reveal mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                <span class="font-semibold text-[#003b70]">Important: </span>{{ $section['callout'] }}
                            </div>
                        @endif
                    </div>
                    <div class="order-2 reveal">
                        <img src="{{ $sectionImages[$index] ?? $heroImage }}" alt="" class="pc-section-image w-full aspect-[4/3] object-cover rounded-2xl" width="800" height="600" loading="lazy" decoding="async" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                    </div>
                @else
                    <div class="order-2 md:order-1 reveal">
                        <img src="{{ $sectionImages[$index] ?? $heroImage }}" alt="" class="pc-section-image w-full aspect-[4/3] object-cover rounded-2xl" width="800" height="600" loading="lazy" decoding="async" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                    </div>
                    <div class="order-1 md:order-2">
                        <h3 class="reveal text-xl font-semibold text-[#003b70] pc-font-display">{{ $section['heading'] }}</h3>
                        <div class="mt-3 space-y-3 text-sm md:text-base text-slate-600 leading-relaxed">
                            @foreach($section['paragraphs'] as $p)
                                <p class="reveal reveal-child">{!! nl2br(e($p)) !!}</p>
                            @endforeach
                        </div>
                        @if(!empty($section['list']))
                            <ul class="mt-4 space-y-2 text-sm md:text-base text-slate-600">
                                @foreach($section['list'] as $item)
                                    <li class="reveal reveal-child flex items-start gap-2.5"><span class="text-[#056dae] mt-0.5">✓</span><span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        @endif
                        @if(!empty($section['callout']))
                            <div class="reveal mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                <span class="font-semibold text-[#003b70]">Important: </span>{{ $section['callout'] }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </section>

    @if(!empty($disclaimer))
        <section class="border-t border-slate-200 pt-6 text-xs text-slate-500 leading-relaxed">
            <p class="font-semibold text-slate-600">Disclosure</p>
            <p class="mt-1.5">{{ $disclaimer }}</p>
        </section>
    @endif
</div>
@endsection
