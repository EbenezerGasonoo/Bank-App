@extends('layouts.public')

@section('title', $title)
@section('page_title', $title)

@section('content')
@php($fallbackImage = asset('images/bank-placeholder.svg'))
<div class="relative z-10 max-w-6xl mx-auto text-[#1a1f26]">
    <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-10 md:mb-12">
        <div class="lg:col-span-7 space-y-5">
            <p class="reveal text-xs uppercase tracking-widest text-[#056dae]">Savings Products</p>
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

    <section class="reveal rounded-2xl border border-slate-200 bg-white p-6 md:p-8 mb-12">
        <div class="grid lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7">
                <h2 class="text-2xl font-semibold text-[#003b70] pc-font-display">FDR maturity calculator</h2>
                <p class="text-sm text-slate-600 mt-2">Estimate maturity proceeds based on deposit amount, rate, and tenure.</p>
                <div class="grid sm:grid-cols-3 gap-4 mt-6">
                    <div>
                        <label for="fdr-principal" class="block text-sm font-medium text-slate-600 mb-1.5">Deposit amount (£)</label>
                        <input id="fdr-principal" type="number" min="100" step="100" value="10000" class="w-full rounded-lg border-slate-300" />
                    </div>
                    <div>
                        <label for="fdr-rate" class="block text-sm font-medium text-slate-600 mb-1.5">Annual rate (%)</label>
                        <input id="fdr-rate" type="number" min="0.1" max="20" step="0.1" value="5.8" class="w-full rounded-lg border-slate-300" />
                    </div>
                    <div>
                        <label for="fdr-term" class="block text-sm font-medium text-slate-600 mb-1.5">Tenure (months)</label>
                        <select id="fdr-term" class="w-full rounded-lg border-slate-300">
                            <option value="3">3 months</option>
                            <option value="6">6 months</option>
                            <option value="12" selected>12 months</option>
                            <option value="24">24 months</option>
                        </select>
                    </div>
                </div>
                <button id="fdr-calc-btn" type="button" class="btn-primary mt-5 px-6 py-3 rounded-lg font-semibold text-white">Calculate maturity</button>
            </div>

            <aside class="lg:col-span-5 rounded-xl border border-slate-200 bg-slate-50 p-5 md:p-6">
                <p class="text-xs uppercase tracking-wider text-slate-500">Estimated maturity</p>
                <p id="fdr-maturity" class="text-3xl font-bold text-[#003b70] mt-2 pc-font-display">£0.00</p>
                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Principal</span>
                        <span id="fdr-principal-out" class="font-semibold text-slate-800">£0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Estimated interest</span>
                        <span id="fdr-interest-out" class="font-semibold text-slate-800">£0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tenure</span>
                        <span id="fdr-tenure-out" class="font-semibold text-slate-800">0 months</span>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="space-y-12 text-slate-600 leading-relaxed">
        @foreach($sections as $section)
            <section class="rounded-xl border border-slate-200 bg-white p-6 md:p-8">
                <h2 class="text-xl font-semibold text-[#003b70] pc-font-display mb-4">{{ $section['heading'] }}</h2>
                <div class="space-y-3 text-[15px] md:text-base">
                    @foreach($section['paragraphs'] as $p)
                        <p>{!! nl2br(e($p)) !!}</p>
                    @endforeach
                </div>
                @if(!empty($section['list']))
                    <ul class="mt-5 space-y-2.5 pl-1 list-none">
                        @foreach($section['list'] as $item)
                            <li class="flex gap-3 text-[15px] md:text-base">
                                <span class="text-[#056dae] mt-1.5 flex-shrink-0" aria-hidden="true">✓</span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if(!empty($section['callout']))
                    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50/90 px-4 py-3 text-sm text-slate-700">
                        <span class="font-medium text-[#003b70]">Note — </span>{{ $section['callout'] }}
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    @if(!empty($disclaimer))
        <div class="mt-10 pt-6 border-t border-slate-200 text-xs text-slate-500 leading-relaxed max-w-3xl">
            <p class="font-medium text-slate-600 mb-1">Important</p>
            <p>{{ $disclaimer }}</p>
        </div>
    @endif
</div>

<script>
    (function () {
        function formatGBP(value) {
            return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(value);
        }

        function calculateFdr() {
            const principal = parseFloat(document.getElementById('fdr-principal').value) || 0;
            const annualRate = parseFloat(document.getElementById('fdr-rate').value) || 0;
            const months = parseInt(document.getElementById('fdr-term').value, 10) || 0;

            if (principal <= 0 || months <= 0) {
                return;
            }

            const interest = principal * (annualRate / 100) * (months / 12);
            const maturity = principal + interest;

            document.getElementById('fdr-maturity').textContent = formatGBP(maturity);
            document.getElementById('fdr-principal-out').textContent = formatGBP(principal);
            document.getElementById('fdr-interest-out').textContent = formatGBP(interest);
            document.getElementById('fdr-tenure-out').textContent = `${months} months`;
        }

        ['fdr-principal', 'fdr-rate', 'fdr-term'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', calculateFdr);
                el.addEventListener('change', calculateFdr);
            }
        });

        const btn = document.getElementById('fdr-calc-btn');
        if (btn) {
            btn.addEventListener('click', calculateFdr);
        }

        calculateFdr();
    })();
</script>
@endsection

