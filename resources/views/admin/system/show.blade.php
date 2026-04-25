@extends('layouts.app')
@section('title', $module['title'] . ' — System Management')
@section('page-title', $module['title'])
@section('page-subtitle', 'System Management / Module')

@section('content')
<div class="space-y-5">
    <div class="glass rounded-2xl border border-slate-200 p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="pc-system-icon" aria-hidden="true">{{ $module['icon'] }}</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $module['title'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $module['description'] }}</p>
                </div>
            </div>
            <a href="{{ route('admin.system.index') }}" class="text-xs font-medium text-[#056dae] hover:underline">Back to modules</a>
        </div>

        <div class="grid md:grid-cols-3 gap-3 mt-5">
            <div class="pc-system-mini-card">
                <p class="pc-system-mini-label">Status</p>
                <p class="pc-system-mini-value">Scaffolded</p>
            </div>
            <div class="pc-system-mini-card">
                <p class="pc-system-mini-label">Scope</p>
                <p class="pc-system-mini-value">Module Controls</p>
            </div>
            <div class="pc-system-mini-card">
                <p class="pc-system-mini-label">Permissions</p>
                <p class="pc-system-mini-value">Admin Only</p>
            </div>
        </div>
    </div>

    @if($module['slug'] === 'general-settings')
        <section class="glass rounded-2xl border border-slate-200 p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-slate-900">General Settings</h3>
            <form method="POST" action="{{ route('admin.system.update', $module['slug']) }}" class="pc-system-settings-grid mt-4">
                @csrf
                @foreach($generalSettingsFields as $field)
                    <label class="pc-system-field">
                        <span class="pc-system-field-label">{{ $field['label'] }} <em>*</em></span>
                        @if(($field['type'] ?? 'text') === 'select')
                            <select name="settings[{{ $field['key'] }}]">
                                @foreach($field['options'] ?? [] as $option)
                                    <option {{ ($generalSettingsValues[$field['key']] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif(($field['type'] ?? 'text') === 'color')
                            <div class="pc-system-color-field">
                                <input type="color" value="#{{ ltrim($generalSettingsValues[$field['key']] ?? '', '#') }}" />
                                <input type="text" name="settings[{{ $field['key'] }}]" value="{{ ltrim($generalSettingsValues[$field['key']] ?? '', '#') }}" />
                            </div>
                        @elseif(isset($field['suffix']))
                            <div class="pc-system-input-addon">
                                <input type="{{ $field['type'] ?? 'text' }}" name="settings[{{ $field['key'] }}]" value="{{ $generalSettingsValues[$field['key']] ?? '' }}" />
                                <span>{{ $field['suffix'] }}</span>
                            </div>
                        @else
                            <input type="{{ $field['type'] ?? 'text' }}" name="settings[{{ $field['key'] }}]" value="{{ $generalSettingsValues[$field['key']] ?? '' }}" />
                        @endif
                    </label>
                @endforeach

                <div class="flex flex-wrap gap-2 pt-4 md:col-span-2 xl:col-span-4">
                    <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg text-xs">Save Changes</button>
                    <button type="reset" class="px-4 py-2 rounded-lg text-xs border border-slate-300 text-slate-700 bg-white">Reset</button>
                </div>
            </form>
        </section>
    @elseif($module['slug'] === 'system-configuration')
        <section class="glass rounded-2xl border border-slate-200 overflow-hidden">
            <div class="pc-system-toggle-list">
                @foreach($systemConfigurationItems as $item)
                    <div class="pc-system-toggle-row">
                        <div class="min-w-0 pr-4">
                            <p class="pc-system-toggle-title">{{ $item['title'] }}</p>
                            <p class="pc-system-toggle-description">{{ $item['description'] }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.system.toggle', $module['slug']) }}">
                            @csrf
                            <input type="hidden" name="key" value="{{ $item['key'] }}" />
                            <input type="hidden" name="enabled" value="{{ $item['enabled'] ? 0 : 1 }}" />
                            <button type="submit" class="pc-system-toggle-btn {{ $item['enabled'] ? 'is-enabled' : 'is-disabled' }}">
                                <span>{{ $item['enabled'] ? 'Enable' : 'Disable' }}</span>
                                <em aria-hidden="true"></em>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @elseif($moduleBlueprint)
        <div class="grid lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
            <section class="glass rounded-2xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-900">{{ $moduleBlueprint['title'] }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $moduleBlueprint['description'] }}</p>

                <form method="POST" action="{{ route('admin.system.update', $module['slug']) }}" class="pc-system-settings-grid mt-4">
                    @csrf
                    @foreach($moduleBlueprint['fields'] as $field)
                        <label class="pc-system-field">
                            <span class="pc-system-field-label">{{ $field['label'] }} <em>*</em></span>
                            @if(($field['type'] ?? 'text') === 'select')
                                <select name="settings[{{ $field['key'] }}]">
                                    @foreach($field['options'] ?? [] as $option)
                                        <option {{ ($field['value'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif(isset($field['suffix']))
                                <div class="pc-system-input-addon">
                                    <input type="{{ $field['type'] ?? 'text' }}" name="settings[{{ $field['key'] }}]" value="{{ $field['value'] ?? '' }}" />
                                    <span>{{ $field['suffix'] }}</span>
                                </div>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" name="settings[{{ $field['key'] }}]" value="{{ $field['value'] ?? '' }}" />
                            @endif
                        </label>
                    @endforeach

                    <div class="flex flex-wrap gap-2 pt-1 md:col-span-2 xl:col-span-4">
                        <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg text-xs">Save Changes</button>
                        <button type="reset" class="px-4 py-2 rounded-lg text-xs border border-slate-300 text-slate-700 bg-white">Reset</button>
                    </div>
                </form>
            </section>

            <aside class="glass rounded-2xl border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-900">Build Checklist</h3>
                <ul class="mt-3 space-y-2">
                    @foreach($comingSoonItems as $item)
                        <li class="text-xs text-slate-600 pc-system-check-item">{{ $item }}</li>
                    @endforeach
                </ul>

                <div class="mt-5 pt-4 border-t border-slate-200">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Other Modules</p>
                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-1">
                        @foreach($modules as $item)
                            <a href="{{ route('admin.system.show', $item['slug']) }}" class="pc-system-inline-link {{ $item['slug'] === $module['slug'] ? 'is-active' : '' }}">
                                <span>{{ $item['icon'] }}</span>
                                <span>{{ $item['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    @else
        <section class="glass rounded-2xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-900">Module Setup</h3>
            <p class="text-xs text-slate-500 mt-1">This module is scaffolded and ready for custom controls.</p>
        </section>
    @endif
</div>
@endsection
