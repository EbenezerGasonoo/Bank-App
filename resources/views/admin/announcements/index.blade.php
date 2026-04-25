@extends('layouts.app')
@section('title', 'Announcements — Admin')
@section('page-title', 'Announcements')

@section('header-actions')
    <a href="{{ route('admin.announcements.create') }}" class="btn-primary text-white text-sm px-5 py-2 rounded-lg font-medium shadow-sm">+ New Announcement</a>
@endsection

@section('content')
<div class="glass rounded-2xl overflow-hidden pc-admin-list-shell">
    <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70">
        <p class="text-sm font-semibold text-slate-900">Announcement Feed</p>
        <p class="text-xs text-slate-500 mt-1">Manage customer-facing notices and publication status.</p>
    </div>
    <table class="pc-admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Author</th>
                <th>Published</th>
                <th>Date</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($announcements as $ann)
            <tr>
                <td class="text-slate-900 font-semibold">{{ $ann->title }}</td>
                <td>
                    <span class="pc-pill {{ $ann->type === 'info' ? 'pc-pill-info' : ($ann->type === 'warning' ? 'pc-pill-warning' : 'pc-pill-alert') }}">
                        {{ ucfirst($ann->type) }}
                    </span>
                </td>
                <td class="text-slate-600 text-sm">{{ optional($ann->author)->name }}</td>
                <td>
                    @if($ann->is_published)
                        <span class="pc-pill pc-pill-live">Live</span>
                    @else
                        <span class="pc-pill pc-pill-draft">Draft</span>
                    @endif
                </td>
                <td class="text-slate-500 text-sm">{{ $ann->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.announcements.edit', $ann) }}" class="pc-action-link pc-action-link-edit">Edit</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                        <button class="pc-action-link pc-action-link-delete">Delete</button>
                    </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-slate-500">No announcements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-200">{{ $announcements->links() }}</div>
</div>
@endsection
