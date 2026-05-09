@extends('layouts.admin')

@section('title', __('messages.assign_mentor') . ' - Admin Panel')

@section('admin-content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-100 md:text-gray-800">{{ __('messages.assign_mentor') }}</h1>
        <p class="text-sm text-gray-300 md:text-gray-500 mt-1">{{ __('messages.allocate_mentors') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.assign_mentor') }}</h2>

        <form method="POST" action="{{ route('admin.mentor-management.assign') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.student') }}</label>
                <select name="student_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">{{ __('messages.select_student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->class_level ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('messages.teacher') }}</label>
                <select name="mentor_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">{{ __('messages.select_mentor') }}</option>
                    @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}">{{ $mentor->name }}{{ $mentor->subject_specialization ? ' - ' . $mentor->subject_specialization : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl font-semibold text-sm transition">
                    {{ __('messages.assign_mentor') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">{{ __('messages.student') }}</th>
                    <th class="px-4 py-3 text-left font-semibold">{{ __('messages.class') }}</th>
                    <th class="px-4 py-3 text-left font-semibold">{{ __('messages.assigned_mentor') }}</th>
                    <th class="px-4 py-3 text-left font-semibold">{{ __('messages.teacher') }} {{ __('messages.contact') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($students as $student)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $student->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $student->class_level ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            @if($student->assignedMentor)
                                {{ $student->assignedMentor->name }}
                            @else
                                <span class="text-gray-400">{{ __('messages.not_assigned') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($student->assignedMentor)
                                <div>{{ $student->assignedMentor->email }}</div>
                                <div class="text-xs text-gray-400">{{ $student->assignedMentor->phone ?? 'No phone' }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-100">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
