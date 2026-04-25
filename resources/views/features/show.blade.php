@extends('layouts.app')
@section('title', $feature['title'])
@section('page-title', $feature['title'])
@section('page-subtitle', 'Customer Services')

@section('content')
<div class="max-w-3xl">
    <section class="glass rounded-2xl p-6 border border-slate-200">
        @if(!empty($feature['comingSoon']))
            <div class="inline-flex items-center rounded-full border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                Coming Soon
            </div>
        @endif
        <h3 class="text-slate-900 font-semibold text-base">{{ $feature['title'] }}</h3>
        <p class="text-sm text-slate-600 mt-2">{{ $feature['description'] }}</p>
        @if(!empty($feature['comingSoon']))
            <p class="text-xs text-slate-500 mt-4">
                This module is under active development and will be available soon.
            </p>
        @else
            <p class="text-xs text-slate-500 mt-4">
                This section is now linked in the customer sidebar and ready for full workflow implementation.
            </p>
        @endif
        <div class="mt-5">
            <a href="{{ route('dashboard') }}" class="btn-primary text-white px-4 py-2 rounded-lg text-sm inline-block">Back to Dashboard</a>
        </div>
    </section>
</div>
@endsection
