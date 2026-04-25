@extends('layouts.app')
@section('title', 'System Management')
@section('page-title', 'System Management')
@section('page-subtitle', 'Configure and control platform modules')

@section('content')
<div class="pc-system-grid">
    @foreach($modules as $module)
        <a href="{{ route('admin.system.show', $module['slug']) }}" class="pc-system-card">
            <div class="pc-system-icon" aria-hidden="true">{{ $module['icon'] }}</div>
            <div>
                <p class="pc-system-title">{{ $module['title'] }}</p>
                <p class="pc-system-description">{{ $module['description'] }}</p>
            </div>
            <span class="pc-system-accent" aria-hidden="true"></span>
        </a>
    @endforeach
</div>
@endsection
