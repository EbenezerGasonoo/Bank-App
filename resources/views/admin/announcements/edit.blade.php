@extends('layouts.app')
@section('title', 'Edit Announcement — Admin')
@section('page-title', 'Edit Announcement')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass rounded-2xl p-8">
        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label>Title</label>
                <input name="title" type="text" required value="{{ old('title', $announcement->title) }}" />
            </div>
            <div>
                <label>Body</label>
                <textarea name="body" rows="5" required>{{ old('body', $announcement->body) }}</textarea>
            </div>
            <div>
                <label>Type</label>
                <select name="type">
                    @foreach(['info','warning','alert'] as $t)
                    <option value="{{ $t }}" {{ $announcement->type === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="is_published" class="w-4 h-4" {{ $announcement->is_published ? 'checked' : '' }}>
                <label for="is_published" class="mb-0 text-sm text-slate-600">Published</label>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="btn-primary text-white px-8 py-3 rounded-lg font-semibold">Update</button>
                <a href="{{ route('admin.announcements.index') }}" class="glass text-slate-600 px-8 py-3 rounded-lg font-semibold border border-slate-200 hover:border-slate-300 transition-all">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
