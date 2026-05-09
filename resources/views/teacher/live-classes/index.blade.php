@extends('layouts.teacher')

@section('teacher-content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Live Classes</h1>
        <a href="{{ route('teacher.live-classes.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + Schedule Class
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg text-sm border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Title</th>
                        <th class="p-4 font-semibold">Scheduled At</th>
                        <th class="p-4 font-semibold">Duration</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($classes as $class)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <p class="font-semibold text-gray-800 text-sm">{{ $class->title }}</p>
                                <p class="text-xs text-gray-500">{{ $class->course ? $class->course->title : 'General' }}</p>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                {{ $class->scheduled_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                {{ $class->duration_minutes }} mins
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    {{ $class->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $class->status === 'live' ? 'bg-red-100 text-red-700 animate-pulse' : '' }}
                                    {{ $class->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $class->status === 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <a href="{{ $class->meeting_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Join</a>
                                <a href="{{ route('teacher.live-classes.edit', $class) }}" class="text-gray-500 hover:text-gray-700 text-sm">Edit</a>
                                <form action="{{ route('teacher.live-classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 text-sm">
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
