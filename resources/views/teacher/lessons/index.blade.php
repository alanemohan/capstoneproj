@extends('layouts.teacher')

@section('title', 'My Lessons - Nabha Learning')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">My Lessons</h1>
            <p class="text-xs text-gray-500 mt-1">Manage your uploaded learning materials.</p>
        </div>
        <a href="{{ route('teacher.lessons.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
            Upload Lesson
        </a>
    </div>

    @if($lessons->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-gray-200 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 mb-2">No Lessons Yet</h3>
            <p class="text-xs text-gray-500 mb-5">Start by uploading your first lesson for students!</p>
            <a href="{{ route('teacher.lessons.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg transition font-bold text-xs uppercase tracking-wider shadow-sm">
                Upload First Lesson
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3 text-left">Lesson</th>
                            <th class="px-5 py-3 text-left">Subject</th>
                            <th class="px-5 py-3 text-left">Class</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Views</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @foreach($lessons as $lesson)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-3.5">
                                    <div>
                                        <p class="font-bold text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ Str::limit($lesson->title, 40) }}</p>
                                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $lesson->created_at->format('d M Y') }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 font-semibold">{{ $lesson->subject }}</td>
                                <td class="px-5 py-3.5 text-gray-600 font-semibold">{{ $lesson->class_level }}</td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-50 border-yellow-250 text-yellow-700',
                                            'approved' => 'bg-blue-50 border-blue-250 text-blue-700',
                                            'published' => 'bg-emerald-50 border-emerald-250 text-emerald-700',
                                            'rejected' => 'bg-red-50 border-red-250 text-red-700',
                                        ];
                                    @endphp
                                    <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md border uppercase tracking-wider {{ $statusClasses[$lesson->status] ?? 'bg-gray-50 border-gray-200 text-gray-600' }}">
                                        {{ $lesson->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center text-gray-900 font-extrabold">{{ $lesson->view_count }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('teacher.lessons.edit', $lesson->id) }}"
                                           class="text-[10px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-md transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('teacher.lessons.destroy', $lesson->id) }}"
                                              onsubmit="return confirm('Delete this lesson?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-150 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-md transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-4">{{ $lessons->links() }}</div>
    @endif
</div>
@endsection
