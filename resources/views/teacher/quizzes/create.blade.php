@extends('layouts.teacher')

@section('title', 'Create Quiz - Nabha Learning')

@section('teacher-content')
<div class="max-w-4xl mx-auto animate-fade-in" x-data="quizBuilder()">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.quizzes') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back</a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Create New Quiz</h1>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-xs leading-relaxed font-semibold">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.quizzes.store') }}" class="space-y-6">
        @csrf

        <!-- Quiz Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-5">Quiz Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Quiz Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                           placeholder="e.g., Chapter 3 - Photosynthesis Quiz">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Subject *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800"
                           placeholder="e.g., Mathematics, Science, Physics">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Class Level *</label>
                    <select name="class_level" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                        <option value="">Select Class</option>
                        @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                            <option value="{{ $class }}" {{ old('class_level') === $class ? 'selected':'' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Related Lesson (optional)</label>
                    <select name="lesson_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                        <option value="">None</option>
                        @foreach($lessons as $id => $title)
                            <option value="{{ $id }}" {{ old('lesson_id') == $id ? 'selected':'' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Time Limit (minutes) *</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit', 30) }}" required min="5" max="180"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Passing Marks (%) *</label>
                    <input type="number" name="passing_marks" value="{{ old('passing_marks', 40) }}" required min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Max Attempts *</label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', 3) }}" required min="1" max="10"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-widest">Questions (<span x-text="questions.length"></span>)</h2>
                <button type="button" @click="addQuestion()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition text-xs shadow-sm">
                    Add Question
                </button>
            </div>

            <div class="space-y-5">
                <template x-for="(q, index) in questions" :key="q.id">
                    <div class="border border-gray-200 rounded-lg p-5 bg-gray-50/50">

                        <!-- Question header -->
                        <div class="flex items-center justify-between mb-4.5 pb-2 border-b border-gray-200">
                            <span class="font-extrabold text-xs text-gray-800 uppercase tracking-wider" x-text="`Question ${index + 1}`"></span>
                            <div class="flex items-center gap-3">
                                <!-- Question Type -->
                                <select :name="`questions[${index}][type]`" x-model="q.type"
                                        @change="onTypeChange(q)"
                                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-gray-700">
                                    <option value="mcq">MCQ</option>
                                    <option value="true_false">True / False</option>
                                    <option value="text">Text Answer</option>
                                </select>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Marks:</label>
                                <input type="number" :name="`questions[${index}][marks]`" x-model="q.marks" min="1" value="1"
                                       class="w-16 px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-800">
                                <button type="button" @click="removeQuestion(index)" x-show="questions.length > 1"
                                        class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase tracking-wider ml-1">Remove</button>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <textarea :name="`questions[${index}][question_text]`" x-model="q.text" required rows="2"
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3 resize-none leading-relaxed text-gray-850"
                                  placeholder="Enter your question..."></textarea>

                        <!-- Hidden input — always submitted, always reflects q.correct -->
                        <input type="hidden" :name="`questions[${index}][correct_answer]`" :value="q.correct">

                        <!-- MCQ Options -->
                        <div x-show="q.type === 'mcq'" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <template x-for="opt in ['a','b','c','d']">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 bg-white border border-gray-250 rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0"
                                          :class="q.correct === opt ? 'border-emerald-600 text-emerald-600' : 'border-gray-200 text-gray-400'"
                                          x-text="opt.toUpperCase()"></span>
                                    <input type="text" :name="`questions[${index}][option_${opt}]`"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-850"
                                           :placeholder="`Option ${opt.toUpperCase()}`"
                                           :required="q.type === 'mcq'">
                                    <input type="radio" :value="opt"
                                           x-model="q.correct"
                                           class="flex-shrink-0 text-emerald-650 focus:ring-emerald-500">
                                </div>
                            </template>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider md:col-span-2 mt-1">Select the radio button next to the option to mark the correct answer.</p>
                        </div>

                        <!-- True / False Options -->
                        <div x-show="q.type === 'true_false'" class="flex gap-5 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="a"
                                       x-model="q.correct" class="text-emerald-650 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-gray-700">True</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" value="b"
                                       x-model="q.correct" class="text-emerald-650 focus:ring-emerald-500">
                                <span class="text-xs font-bold text-gray-700">False</span>
                            </label>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider self-center ml-2">Select the correct answer</p>
                        </div>

                        <!-- Text Answer -->
                        <div x-show="q.type === 'text'" class="mb-4">
                            <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Expected Correct Answer *</label>
                            <input type="text" x-model="q.correct"
                                   class="w-full px-3 py-2.5 border border-emerald-200 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 bg-emerald-50/20 text-gray-850"
                                   placeholder="Type the expected answer (student's answer will be compared)">
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1.5">Comparison is case-insensitive. Keep it simple and precise.</p>
                        </div>

                        <!-- Explanation -->
                        <input type="text" :name="`questions[${index}][explanation]`"
                               class="w-full px-3 py-2 border border-yellow-200 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 bg-yellow-50/20 text-gray-850"
                               placeholder="Explanation (optional — shown to students after quiz)">
                    </div>
                </template>
            </div>

            <button type="button" @click="addQuestion()"
                    class="mt-5 w-full border border-dashed border-gray-300 hover:border-emerald-500/35 hover:bg-emerald-50/10 text-gray-400 hover:text-emerald-600 py-3.5 rounded-lg text-xs font-bold uppercase tracking-wider transition">
                Add Another Question
            </button>
        </div>

        <div class="flex gap-3 pb-20">
            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3.5 rounded-lg transition-all text-xs shadow-sm">
                Save Quiz
            </button>
            <a href="{{ route('teacher.quizzes') }}" class="px-6 py-3.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition font-bold text-xs uppercase tracking-wider flex items-center">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function quizBuilder() {
    return {
        questions: [{ id: 1, text: '', type: 'mcq', correct: 'a', marks: 1 }],
        nextId: 2,
        addQuestion() {
            this.questions.push({ id: this.nextId++, text: '', type: 'mcq', correct: 'a', marks: 1 });
        },
        removeQuestion(index) {
            if (this.questions.length > 1) {
                this.questions.splice(index, 1);
            }
        },
        onTypeChange(q) {
            if (q.type === 'mcq') q.correct = 'a';
            if (q.type === 'true_false') q.correct = 'a';
            if (q.type === 'text') q.correct = '';
        }
    }
}
</script>
@endpush
@endsection
