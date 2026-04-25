@extends('layouts.app')
@section('title', 'Login Activity')
@section('page-title', 'Login Activity')
@section('page-subtitle', 'See who logged in, from where, and which IP')

@section('content')
<div class="space-y-6">
    <form method="GET" class="glass rounded-2xl p-4 grid md:grid-cols-3 gap-3">
        <input name="email" value="{{ request('email') }}" type="text" placeholder="Filter by email" />
        <input name="ip" value="{{ request('ip') }}" type="text" placeholder="Filter by IP address" />
        <button class="btn-primary text-white rounded-lg py-2 px-4 text-sm font-semibold">Apply Filters</button>
    </form>

    <div class="glass rounded-2xl p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                        <th class="py-3 pr-4">When</th>
                        <th class="py-3 pr-4">User</th>
                        <th class="py-3 pr-4">Role</th>
                        <th class="py-3 pr-4">IP</th>
                        <th class="py-3">Device / Browser</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="py-3 pr-4 text-xs text-slate-600">{{ $activity->logged_in_at->format('d M Y H:i:s') }}</td>
                        <td class="py-3 pr-4">
                            <p class="text-slate-900">{{ $activity->email }}</p>
                            <p class="text-xs text-slate-500">{{ $activity->user?->name ?? 'Unknown User' }}</p>
                        </td>
                        <td class="py-3 pr-4 text-slate-600 capitalize">{{ $activity->role ?? '—' }}</td>
                        <td class="py-3 pr-4 text-slate-700 font-mono text-xs">{{ $activity->ip_address ?? '—' }}</td>
                        <td class="py-3 text-xs text-slate-500">{{ $activity->user_agent ?? 'Unknown device' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-500">No login activity recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $activities->links() }}</div>
    </div>
</div>
@endsection

