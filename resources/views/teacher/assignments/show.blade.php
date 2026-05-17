@extends('layouts.teacher')

@section('title', 'Assignment Details - Nabha Learning')

@section('teacher-content')
<div class="max-w-6xl mx-auto space-y-6 animate-fade-in">
    <div class="flex justify-between items-center pb-5 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('teacher.assignments.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back</a>
                <span class="text-[9px] font-bold bg-indigo-50 border border-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-md uppercase tracking-wider">{{ $assignment->batch->name }}</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight mt-2" style="font-family: var(--font-display);">{{ $assignment->title }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Due Date: <span class="font-bold text-gray-800">{{ $assignment->due_date->format('M d, Y, h:i A') }}</span>
                @if($assignment->due_date->isPast())
                    <span class="text-red-500 font-extrabold ml-1 uppercase text-[9px] tracking-wider bg-red-50 border border-red-150 px-2 py-0.5 rounded-md">Past Due</span>
                @else
                    <span class="text-emerald-650 font-extrabold ml-1 uppercase text-[9px] tracking-wider bg-emerald-50 border border-emerald-150 px-2 py-0.5 rounded-md">Active</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Edit Assignment
            </a>
            <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Delete this assignment?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-50 border border-red-150 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-755 p-4.5 rounded-lg text-xs font-semibold border border-emerald-250">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-xs font-bold text-gray-850 uppercase tracking-widest mb-4">Instructions</h2>
                <div class="text-xs text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $assignment->description }}</div>
            </div>

            <!-- Submissions Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xs font-bold text-gray-850 uppercase tracking-wider">Student Submissions</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                                <th class="px-5 py-3 text-left">Student</th>
                                <th class="px-5 py-3 text-left">Submitted At</th>
                                <th class="px-5 py-3 text-left">File</th>
                                <th class="px-5 py-3 text-center">Marks</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150">
                            @forelse($assignment->submissions as $submission)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $submission->student->avatar_url }}" class="w-6 h-6 rounded-md" alt="Avatar">
                                            <span class="font-bold text-gray-900">{{ $submission->student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-500 font-semibold">{{ $submission->created_at->format('M d, Y') }}</td>
                                    <td class="px-5 py-4">
                                        @if($submission->file_path)
                                            <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="text-emerald-650 hover:underline font-bold flex items-center gap-1">
                                                📄 View File
                                            </a>
                                        @else
                                            <span class="text-gray-300 italic">No attachment</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center font-extrabold text-gray-900">
                                        @if($submission->status === 'graded')
                                            {{ $submission->marks }} / {{ $assignment->max_marks }}
                                        @else
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-yellow-50 text-yellow-750 border border-yellow-250">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button onclick="openGradingModal({{ $submission->id }}, '{{ $submission->student->name }}', '{{ $submission->marks }}', '{{ $submission->feedback }}')"
                                                class="text-[10px] font-bold uppercase tracking-wider text-emerald-650 hover:text-emerald-800 bg-emerald-50 border border-emerald-150 px-3 py-1.5 rounded-md transition">
                                            Grade
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">No submissions received yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary Info Column -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-4.5">
                <h2 class="text-xs font-bold text-gray-850 uppercase tracking-widest border-b border-gray-150 pb-3">Assignment Summary</h2>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Max Marks</span>
                    <span class="font-extrabold text-gray-900 tabular-nums">{{ $assignment->max_marks }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Total Submissions</span>
                    <span class="font-extrabold text-gray-900 tabular-nums">{{ $assignment->submissions->count() }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Graded</span>
                    <span class="font-extrabold text-gray-900 tabular-nums">{{ $assignment->submissions->where('status', 'graded')->count() }}</span>
                </div>

                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Pending Grading</span>
                    <span class="font-extrabold text-yellow-600 tabular-nums">{{ $assignment->submissions->where('status', 'pending')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Vanilla JS Modal for Grading -->
<div id="gradingModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-550/40 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeGradingModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-xl text-left border border-gray-200 shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="gradingForm" method="POST" action="" class="space-y-4 p-6">
                @csrf
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-200 pb-3" id="modalStudentName">Grade Submission</h3>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Marks Given (Max: {{ $assignment->max_marks }}) *</label>
                    <input type="number" name="marks" id="gradingMarks" required min="0" max="{{ $assignment->max_marks }}" step="0.5"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Feedback</label>
                    <textarea name="feedback" id="gradingFeedback" rows="4"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-850 resize-none leading-relaxed"
                              placeholder="Enter feedback for the student..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="closeGradingModal()" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider text-gray-600 border border-gray-300 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                        Submit Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openGradingModal(submissionId, studentName, currentMarks, currentFeedback) {
    const actionUrl = `/teacher/assignments/{{ $assignment->id }}/submissions/` + submissionId + `/grade`;
    document.getElementById('gradingForm').action = actionUrl;
    document.getElementById('modalStudentName').textContent = 'Grade submission for ' + studentName;
    document.getElementById('gradingMarks').value = currentMarks || '';
    document.getElementById('gradingFeedback').value = currentFeedback || '';
    document.getElementById('gradingModal').classList.remove('hidden');
}

function closeGradingModal() {
    document.getElementById('gradingModal').classList.add('hidden');
}
</script>
@endsection
