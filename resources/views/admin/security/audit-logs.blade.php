@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Who changed what and when')

@section('content')
<div class="space-y-6">
    <form method="GET" class="glass rounded-2xl p-4 grid md:grid-cols-3 gap-3">
        <input name="action" value="{{ request('action') }}" type="text" placeholder="Filter by action" />
        <input name="user" value="{{ request('user') }}" type="text" placeholder="Filter by user/email" />
        <button class="btn-primary text-white rounded-lg py-2 px-4 text-sm font-semibold">Apply Filters</button>
    </form>

    <div class="glass rounded-2xl p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">When</th>
                        <th class="py-3 pr-4">Admin</th>
                        <th class="py-3 pr-4">Action</th>
                        <th class="py-3 pr-4">Target</th>
                        <th class="py-3 pr-4">IP</th>
                        <th class="py-3">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="py-3 pr-4 text-slate-600 text-xs">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="py-3 pr-4 text-slate-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="py-3 pr-4 text-slate-700">{{ $log->action }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $log->model_type ? $log->model_type . ' #' . $log->model_id : '—' }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $log->ip_address ?? '—' }}</td>
                        <td class="py-3 text-xs text-slate-500 font-mono">{{ json_encode($log->changes ?? []) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-slate-500">No audit logs available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection

