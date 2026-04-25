@extends('layouts.public')

@section('title', $title)
@section('page_title', $title)

@section('content')
    @php($fallbackImage = asset('images/bank-placeholder.svg'))
    <div class="relative z-10 max-w-6xl mx-auto text-[#1a1f26]">
    {{-- Lead: copy + hero image --}}
    <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-12 md:mb-16">
        <div class="lg:col-span-7 space-y-5">
            <p class="reveal text-xs uppercase tracking-widest text-[#056dae]">Poise Commerce Bank</p>
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

    {{-- Sections: alternating text / imagery --}}
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
                            <img
                                src="{{ $sectionImages[$index] ?? $heroImage }}"
                                alt=""
                                class="pc-section-image w-full h-auto min-h-[200px] object-cover aspect-[4/3]"
                                width="800"
                                height="600"
                                loading="lazy"
                                decoding="async"
                                role="presentation"
                                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                            />
                        </div>
                    @else
                        <div class="order-2 md:order-1 reveal overflow-hidden rounded-xl group">
                            <img
                                src="{{ $sectionImages[$index] ?? $heroImage }}"
                                alt=""
                                class="pc-section-image w-full h-auto min-h-[200px] object-cover aspect-[4/3]"
                                width="800"
                                height="600"
                                loading="lazy"
                                decoding="async"
                                role="presentation"
                                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                            />
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

    <div class="reveal mt-10 flex flex-wrap items-center gap-4">
        <a href="{{ route('register') }}" class="btn-primary btn-primary-shine inline-block text-white px-6 py-3 rounded-lg font-semibold">Open an account</a>
        <a href="{{ route('login') }}" class="text-sm text-[#056dae] font-medium hover:underline">Sign in to online banking</a>
        <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-[#003b70] transition-colors">← Back to home</a>
    </div>
</div>
@endsection
