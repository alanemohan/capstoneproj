@extends('layouts.teacher')

@section('teacher-content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Notice Board</h1>
        <a href="{{ route('teacher.announcements.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + Post Announcement
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg text-sm border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative group">
                <form action="{{ route('teacher.announcements.destroy', $announcement) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('Delete this announcement?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>

                <div class="mb-3">
                    <span class="text-xs font-medium px-2 py-1 rounded bg-gray-100 text-gray-600">
                        {{ $announcement->course ? $announcement->course->title : 'All Students' }}
                    </span>
                    <span class="text-xs text-gray-400 ml-2">{{ $announcement->created_at->diffForHumans() }}</span>
                </div>
                
                <h3 class="font-bold text-gray-900 mb-2">{{ $announcement->title }}</h3>
                <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $announcement->content }}</p>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                No announcements posted yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
