@extends('layouts.admin')

@section('title', __('messages.assign_mentor') . ' - Admin Panel')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="pb-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.assign_mentor') }}</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">{{ __('messages.allocate_mentors') }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('messages.assign_mentor') }}</h2>

        <form method="POST" action="{{ route('admin.mentor-management.assign') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @csrf
            <div>
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.student') }}</label>
                <select name="student_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                    <option value="">{{ __('messages.select_student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->class_level ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">{{ __('messages.teacher') }}</label>
                <select name="mentor_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 bg-white transition">
                    <option value="">{{ __('messages.select_mentor') }}</option>
                    @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}">{{ $mentor->name }}{{ $mentor->subject_specialization ? ' - ' . $mentor->subject_specialization : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    {{ __('messages.assign_mentor') }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">{{ __('messages.student') }}</th>
                        <th class="px-5 py-3">{{ __('messages.class') }}</th>
                        <th class="px-5 py-3">{{ __('messages.assigned_mentor') }}</th>
                        <th class="px-5 py-3">{{ __('messages.teacher') }} {{ __('messages.contact') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">{{ $student->name }}</td>
                            <td class="px-5 py-4 text-slate-600 font-semibold">{{ $student->class_level ?? 'N/A' }}</td>
                            <td class="px-5 py-4">
                                @if($student->assignedMentor)
                                    <span class="text-xs font-bold text-slate-800">{{ $student->assignedMentor->name }}</span>
                                @else
                                    <span class="text-slate-400 font-medium italic">{{ __('messages.not_assigned') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($student->assignedMentor)
                                    <div class="font-bold text-slate-700">{{ $student->assignedMentor->email }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $student->assignedMentor->phone ?? 'No phone' }}</div>
                                @else
                                    <span class="text-slate-400 font-semibold">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-semibold">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
