@extends('layouts.app')
@section('title', 'New Announcement — Admin')
@section('page-title', 'New Announcement')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-6">
            @csrf
            <div>
                <label>Title</label>
                <input name="title" type="text" required value="{{ old('title') }}" placeholder="Announcement title" />
                @error('title')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label>Body</label>
                <textarea name="body" rows="5" required placeholder="Announcement body...">{{ old('body') }}</textarea>
                @error('body')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label>Type</label>
                <select name="type">
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="alert">Alert</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="is_published" class="w-4 h-4" {{ old('is_published') ? 'checked' : '' }}>
                <label for="is_published" class="mb-0 text-sm text-slate-600">Publish immediately</label>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="btn-primary text-white px-8 py-3 rounded-lg font-semibold">Publish</button>
                <a href="{{ route('admin.announcements.index') }}" class="glass text-slate-600 px-8 py-3 rounded-lg font-semibold border border-slate-200 hover:border-slate-300 transition-all">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
