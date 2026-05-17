@extends('layouts.teacher')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex justify-between items-center pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Notice Board</h1>
            <p class="text-xs text-gray-500 mt-1">Post updates and notifications for your students and courses.</p>
        </div>
        <a href="{{ route('teacher.announcements.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
            + Post Announcement
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-755 p-4 rounded-lg text-xs font-semibold border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-xl border border-gray-200 p-5 relative group transition duration-300 hover:border-emerald-500/20 shadow-sm">
                <form action="{{ route('teacher.announcements.destroy', $announcement) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('Delete this announcement?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-1.5 rounded-md border border-red-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>

                <div class="mb-3.5 flex items-center justify-between">
                    <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md border uppercase tracking-wider bg-gray-50 border-gray-150 text-gray-500">
                        {{ $announcement->course ? Str::limit($announcement->course->title, 20) : 'All Students' }}
                    </span>
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">{{ $announcement->created_at->diffForHumans() }}</span>
                </div>
                
                <h3 class="font-bold text-gray-900 text-xs mb-2 leading-snug" style="font-family: var(--font-display);">{{ $announcement->title }}</h3>
                <p class="text-xs text-gray-550 leading-relaxed whitespace-pre-wrap">{{ $announcement->content }}</p>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-450 text-xs shadow-sm">
                No announcements posted yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
