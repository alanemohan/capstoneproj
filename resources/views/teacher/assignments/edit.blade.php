@extends('layouts.teacher')

@section('title', 'Edit Assignment - Nabha Learning')

@section('teacher-content')
<div class="max-w-2xl mx-auto animate-fade-in">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.assignments.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back</a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Edit Assignment</h1>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-xs leading-relaxed font-semibold">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.assignments.update', $assignment) }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Target Batch *</label>
            <select name="batch_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-805 bg-white">
                <option value="">Select Batch</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ old('batch_id', $assignment->batch_id) == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Assignment Title *</label>
            <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808"
                   placeholder="e.g., Algebra Homework Set 1">
        </div>

        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Instructions / Description *</label>
            <textarea name="description" rows="5" required
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-850 resize-none leading-relaxed"
                      placeholder="Enter detailed instructions, questions, or guidelines for the students...">{{ old('description', $assignment->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Due Date *</label>
                <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Maximum Marks *</label>
                <input type="number" name="max_marks" value="{{ old('max_marks', $assignment->max_marks) }}" required min="1"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808"
                       placeholder="e.g., 100">
            </div>
        </div>

        <div class="flex gap-3 pt-3">
            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3 rounded-lg transition text-xs shadow-sm">
                Save Changes
            </button>
            <a href="{{ route('teacher.assignments.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition font-bold text-xs uppercase tracking-wider flex items-center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
