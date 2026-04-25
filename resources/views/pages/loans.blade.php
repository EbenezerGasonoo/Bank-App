@extends('layouts.public')

@section('title', $title)
@section('page_title', $title)

@section('content')
@php($fallbackImage = asset('images/bank-placeholder.svg'))
<div class="relative z-10 max-w-6xl mx-auto text-[#1a1f26]">
    <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-10 md:mb-12">
        <div class="lg:col-span-7 space-y-5">
            <p class="reveal text-xs uppercase tracking-widest text-[#056dae]">Loans & Credit</p>
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

    <section class="reveal pc-loan-calculator-card rounded-2xl p-6 md:p-8 mb-12">
        <div class="grid lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7">
                <h2 class="text-2xl font-semibold text-[#003b70] pc-font-display">Loan calculator</h2>
                <p class="text-sm text-slate-600 mt-2">Estimate repayments before you apply. Figures are illustrative and do not constitute an offer.</p>
                <div class="grid sm:grid-cols-3 gap-4 mt-6">
                    <div>
                        <label for="loan-amount" class="block text-sm font-medium text-slate-600 mb-1.5">Loan amount (£)</label>
                        <input id="loan-amount" type="number" min="100" step="100" value="15000" class="w-full rounded-lg border-slate-300" />
                    </div>
                    <div>
                        <label for="loan-rate" class="block text-sm font-medium text-slate-600 mb-1.5">Annual rate (%)</label>
                        <input id="loan-rate" type="number" min="0.1" max="60" step="0.1" value="8.9" class="w-full rounded-lg border-slate-300" />
                    </div>
                    <div>
                        <label for="loan-term" class="block text-sm font-medium text-slate-600 mb-1.5">Term (years)</label>
                        <input id="loan-term" type="number" min="1" max="40" step="1" value="5" class="w-full rounded-lg border-slate-300" />
                    </div>
                </div>
                <button id="loan-calc-btn" type="button" class="btn-primary mt-5 px-6 py-3 rounded-lg font-semibold text-white">Calculate</button>
            </div>
            <aside class="lg:col-span-5 rounded-xl border border-slate-200 bg-white p-5 md:p-6">
                <p class="text-xs uppercase tracking-wider text-slate-500">Estimated repayment</p>
                <p id="loan-monthly" class="text-3xl font-bold text-[#003b70] mt-2 pc-font-display">£0.00 / month</p>
                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total repayment</span>
                        <span id="loan-total" class="font-semibold text-slate-800">£0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total interest</span>
                        <span id="loan-interest" class="font-semibold text-slate-800">£0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Monthly rate</span>
                        <span id="loan-monthly-rate" class="font-semibold text-slate-800">0.00%</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-5 leading-relaxed">Final rates and terms depend on status, affordability, and product type at application.</p>
            </aside>
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

<script>
    (function () {
        function formatGBP(value) {
            return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(value);
        }

        function calculateLoan() {
            const amount = parseFloat(document.getElementById('loan-amount').value) || 0;
            const annualRate = parseFloat(document.getElementById('loan-rate').value) || 0;
            const years = parseFloat(document.getElementById('loan-term').value) || 0;

            const months = years * 12;
            const monthlyRate = (annualRate / 100) / 12;

            if (amount <= 0 || months <= 0) {
                return;
            }

            let monthlyPayment;
            if (monthlyRate === 0) {
                monthlyPayment = amount / months;
            } else {
                const factor = Math.pow(1 + monthlyRate, months);
                monthlyPayment = amount * (monthlyRate * factor) / (factor - 1);
            }

            const totalRepayment = monthlyPayment * months;
            const totalInterest = totalRepayment - amount;

            document.getElementById('loan-monthly').textContent = `${formatGBP(monthlyPayment)} / month`;
            document.getElementById('loan-total').textContent = formatGBP(totalRepayment);
            document.getElementById('loan-interest').textContent = formatGBP(totalInterest);
            document.getElementById('loan-monthly-rate').textContent = `${(monthlyRate * 100).toFixed(2)}%`;
        }

        const calcButton = document.getElementById('loan-calc-btn');
        ['loan-amount', 'loan-rate', 'loan-term'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', calculateLoan);
            }
        });
        if (calcButton) {
            calcButton.addEventListener('click', calculateLoan);
        }

        calculateLoan();
    })();
</script>
@endsection
