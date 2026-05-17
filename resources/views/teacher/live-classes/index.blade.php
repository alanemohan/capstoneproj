@extends('layouts.teacher')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex justify-between items-center pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Live Classes</h1>
            <p class="text-xs text-gray-500 mt-1">Schedule and manage real-time online classes.</p>
        </div>
        <a href="{{ route('teacher.live-classes.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
            + Schedule Class
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-755 p-4.5 rounded-lg text-xs font-semibold border border-emerald-250 animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3 text-left">Title</th>
                        <th class="px-5 py-3 text-left">Scheduled At</th>
                        <th class="px-5 py-3 text-left">Duration</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150">
                    @forelse($classes as $class)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ $class->title }}</p>
                                <p class="text-[10px] text-gray-400 font-semibold mt-1">{{ $class->course ? $class->course->title : 'General' }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-650 font-semibold">
                                {{ $class->scheduled_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-5 py-4 text-gray-650 font-bold tabular-nums">
                                {{ $class->duration_minutes }} mins
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md border uppercase tracking-wider
                                    {{ $class->status === 'scheduled' ? 'bg-blue-50 border-blue-250 text-blue-700' : '' }}
                                    {{ $class->status === 'live' ? 'bg-red-50 border-red-250 text-red-750 animate-pulse' : '' }}
                                    {{ $class->status === 'completed' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : '' }}
                                    {{ $class->status === 'cancelled' ? 'bg-gray-50 border-gray-200 text-gray-600' : '' }}">
                                    {{ $class->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $class->meeting_link }}" target="_blank" class="text-[10px] font-bold uppercase tracking-wider bg-emerald-50 border border-emerald-150 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-md transition">Join</a>
                                    <a href="{{ route('teacher.live-classes.edit', $class) }}" class="text-[10px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-md transition">Edit</a>
                                    <form action="{{ route('teacher.live-classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-150 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-md transition">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-semibold">
                                No live classes scheduled yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
