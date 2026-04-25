@extends('layouts.public')

@section('title', $title)
@section('page_title', $title)

@section('content')
@php($fallbackImage = asset('images/bank-placeholder.svg'))
<div class="relative z-10 max-w-6xl mx-auto text-[#1a1f26]">
    <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-10 md:mb-12">
        <div class="lg:col-span-7 space-y-5">
            <p class="reveal text-xs uppercase tracking-widest text-[#056dae]">Cards</p>
            <h1 class="reveal reveal-stagger-1 text-3xl md:text-4xl lg:text-5xl font-bold text-[#003b70] leading-tight pc-font-display">{{ $headline }}</h1>
            <p class="reveal reveal-stagger-2 text-lg text-slate-600 leading-relaxed border-l-[3px] border-[#c41230] pl-5 py-1">{{ $intro }}</p>
        </div>
        <div class="lg:col-span-5 mt-2 lg:mt-0">
            <div class="reveal reveal-stagger-2 pc-hero-image-wrap aspect-[4/3] lg:aspect-[3/4] group">
                <img
                    src="{{ $heroImage }}"
                    alt="{{ $title }} — Poise Commerce Bank"
                    class="pc-hero-image w-full h-full min-h-[220px] object-cover"
                    width="800"
                    height="600"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                    onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                />
            </div>
        </div>
    </div>

    <section class="mb-12">
        <h2 class="text-2xl font-semibold text-[#003b70] pc-font-display mb-5">Card mockups</h2>
        <div class="grid lg:grid-cols-3 gap-5">
            <article class="reveal pc-card-mockup pc-card-mockup-platinum rounded-2xl p-5">
                <div class="flex items-center justify-between text-white/90 text-xs uppercase tracking-wider">
                    <span>Poise Platinum</span><span>VISA</span>
                </div>
                <p class="mt-8 text-white/80 text-[11px] tracking-[0.18em]">**** **** **** 1142</p>
                <div class="mt-6 flex items-end justify-between">
                    <div>
                        <p class="text-white text-sm font-semibold">J. MORGAN</p>
                        <p class="text-white/70 text-xs">Valid thru 07/31</p>
                    </div>
                    <span class="text-white/90 text-xs">Personal</span>
                </div>
            </article>

            <article class="reveal reveal-stagger-1 pc-card-mockup pc-card-mockup-rewards rounded-2xl p-5">
                <div class="flex items-center justify-between text-white/90 text-xs uppercase tracking-wider">
                    <span>Poise Rewards+</span><span>Mastercard</span>
                </div>
                <p class="mt-8 text-white/80 text-[11px] tracking-[0.18em]">**** **** **** 4487</p>
                <div class="mt-6 flex items-end justify-between">
                    <div>
                        <p class="text-white text-sm font-semibold">A. BENNETT</p>
                        <p class="text-white/70 text-xs">Valid thru 11/30</p>
                    </div>
                    <span class="text-white/90 text-xs">Cashback</span>
                </div>
            </article>

            <article class="reveal reveal-stagger-2 pc-card-mockup pc-card-mockup-business rounded-2xl p-5">
                <div class="flex items-center justify-between text-white/90 text-xs uppercase tracking-wider">
                    <span>Poise Business</span><span>VISA</span>
                </div>
                <p class="mt-8 text-white/80 text-[11px] tracking-[0.18em]">**** **** **** 8821</p>
                <div class="mt-6 flex items-end justify-between">
                    <div>
                        <p class="text-white text-sm font-semibold">NORTHRIDGE LTD</p>
                        <p class="text-white/70 text-xs">Valid thru 03/32</p>
                    </div>
                    <span class="text-white/90 text-xs">Commercial</span>
                </div>
            </article>
        </div>
    </section>

    <section class="mb-12">
        <div class="grid md:grid-cols-3 gap-4">
            @foreach([
                ['Built-in controls', 'Freeze, unfreeze, and monitor card usage instantly from online banking.'],
                ['Fraud monitoring', 'Live risk checks and unusual-spend alerts with rapid dispute pathways.'],
                ['Digital wallet ready', 'Add eligible cards to mobile wallets for contactless transactions.'],
            ] as [$titleItem, $copy])
                <article class="reveal pc-cards-feature rounded-xl p-5">
                    <h3 class="text-base font-semibold text-[#003b70]">{{ $titleItem }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $copy }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-semibold text-[#003b70] pc-font-display mb-5">Compare card tiers</h2>
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
            <table class="w-full min-w-[760px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Feature</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Everyday Debit</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Rewards Credit</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Business Card</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['Contactless payments', 'Included', 'Included', 'Included'],
                        ['Virtual card support', 'Included', 'Included', 'Included'],
                        ['Rewards programme', 'Not included', 'Points and cashback', 'Spend rebates'],
                        ['Spending limits', 'Daily limit controls', 'Credit limit + instalments', 'Team limits and policy controls'],
                        ['Travel benefits', 'Standard network benefits', 'Enhanced travel package', 'Business travel package'],
                    ] as [$feature, $a, $b, $c])
                    <tr class="border-t border-slate-200">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $feature }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $a }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $b }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $c }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="space-y-16 md:space-y-20 text-slate-600 leading-relaxed">
        @foreach($sections as $index => $section)
            <section>
                <div class="grid md:grid-cols-2 gap-8 lg:gap-10 items-center">
                    @if($index % 2 === 0)
                        <div class="order-1 space-y-4">
                            <h2 class="reveal text-xl font-semibold text-[#003b70] pb-2 border-b border-slate-200 md:border-0 md:pb-0 pc-font-display">{{ $section['heading'] }}</h2>
                            <div class="space-y-4 text-[15px] md:text-base text-slate-600">
                                @foreach($section['paragraphs'] as $pi => $p)
                                    <p class="reveal reveal-child @if($pi) reveal-stagger-{{ min($pi, 3) }} @endif">{!! nl2br(e($p)) !!}</p>
                                @endforeach
                            </div>
                            @if(!empty($section['list']))
                                <ul class="mt-5 space-y-2.5 pl-1 list-none">
                                    @foreach($section['list'] as $li => $item)
                                        <li class="reveal reveal-child flex gap-3 text-[15px] md:text-base @if($li) reveal-stagger-{{ min($li, 3) }} @endif">
                                            <span class="text-[#056dae] mt-1.5 flex-shrink-0" aria-hidden="true">✓</span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(!empty($section['callout']))
                                <div class="reveal mt-6 rounded-xl border border-slate-200 bg-slate-50/90 px-4 py-3 text-sm text-slate-700">
                                    <span class="font-medium text-[#003b70]">Note — </span>{{ $section['callout'] }}
                                </div>
                            @endif
                        </div>
                        <div class="order-2 reveal overflow-hidden rounded-xl group">
                            <img src="{{ $sectionImages[$index] ?? $heroImage }}" alt="" class="pc-section-image w-full h-auto min-h-[200px] object-cover aspect-[4/3]" width="800" height="600" loading="lazy" decoding="async" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                        </div>
                    @else
                        <div class="order-2 md:order-1 reveal overflow-hidden rounded-xl group">
                            <img src="{{ $sectionImages[$index] ?? $heroImage }}" alt="" class="pc-section-image w-full h-auto min-h-[200px] object-cover aspect-[4/3]" width="800" height="600" loading="lazy" decoding="async" role="presentation" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" />
                        </div>
                        <div class="order-1 md:order-2 space-y-4">
                            <h2 class="reveal text-xl font-semibold text-[#003b70] pb-2 border-b border-slate-200 md:border-0 md:pb-0 pc-font-display">{{ $section['heading'] }}</h2>
                            <div class="space-y-4 text-[15px] md:text-base text-slate-600">
                                @foreach($section['paragraphs'] as $pi => $p)
                                    <p class="reveal reveal-child @if($pi) reveal-stagger-{{ min($pi, 3) }} @endif">{!! nl2br(e($p)) !!}</p>
                                @endforeach
                            </div>
                            @if(!empty($section['list']))
                                <ul class="mt-5 space-y-2.5 pl-1 list-none">
                                    @foreach($section['list'] as $li => $item)
                                        <li class="reveal reveal-child flex gap-3 text-[15px] md:text-base @if($li) reveal-stagger-{{ min($li, 3) }} @endif">
                                            <span class="text-[#056dae] mt-1.5 flex-shrink-0" aria-hidden="true">✓</span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(!empty($section['callout']))
                                <div class="reveal mt-6 rounded-xl border border-slate-200 bg-slate-50/90 px-4 py-3 text-sm text-slate-700">
                                    <span class="font-medium text-[#003b70]">Note — </span>{{ $section['callout'] }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        @endforeach
    </div>

    @if(!empty($disclaimer))
        <div class="reveal mt-12 md:mt-16 pt-8 border-t border-slate-200 text-xs text-slate-500 leading-relaxed max-w-3xl">
            <p class="font-medium text-slate-600 mb-1">Important</p>
            <p>{{ $disclaimer }}</p>
        </div>
    @endif
</div>
@endsection
