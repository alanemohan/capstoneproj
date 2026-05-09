<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('teacher_id', auth()->id())
            ->withCount(['questions', 'attempts'])
            ->with('lesson')
            ->latest()->paginate(15);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $lessons = Lesson::where('teacher_id', auth()->id())
            ->where('status', 'published')->pluck('title', 'id');
        return view('teacher.quizzes.create', compact('lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'subject'      => ['required', 'string', 'max:255'],
            'class_level'  => ['required', 'string'],
            'lesson_id'    => ['nullable', 'exists:lessons,id'],
            'time_limit'   => ['required', 'integer', 'min:5', 'max:180'],
            'passing_marks'=> ['required', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'questions'    => ['required', 'array', 'min:1'],
            'questions.*.question_text'   => ['required', 'string'],
            'questions.*.type'            => ['required', 'in:mcq,true_false,text'],
            'questions.*.marks'           => ['required', 'integer', 'min:1'],
            'questions.*.correct_answer'  => ['required', 'string'],
            'questions.*.explanation'     => ['nullable', 'string'],
        ]);

        $quiz = Quiz::create([
            'teacher_id'    => auth()->id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'subject'       => $request->subject,
            'class_level'   => $request->class_level,
            'lesson_id'     => $request->lesson_id,
            'time_limit'    => $request->time_limit,
            'passing_marks' => $request->passing_marks,
            'max_attempts'  => $request->max_attempts,
            'status'        => 'draft',
        ]);

        $totalMarks = 0;
        foreach ($request->questions as $index => $qData) {
            $type = $qData['type'];

            $optionA = $optionB = $optionC = $optionD = null;
            $correctAnswer = $qData['correct_answer'];

            if ($type === 'mcq') {
                $optionA = $qData['option_a'] ?? null;
                $optionB = $qData['option_b'] ?? null;
                $optionC = $qData['option_c'] ?? null;
                $optionD = $qData['option_d'] ?? null;
            } elseif ($type === 'true_false') {
                $optionA = 'True';
                $optionB = 'False';
            }
            // text type: no options needed

            Question::create([
                'quiz_id'       => $quiz->id,
                'question_text' => $qData['question_text'],
                'type'          => $type,
                'option_a'      => $optionA,
                'option_b'      => $optionB,
                'option_c'      => $optionC,
                'option_d'      => $optionD,
                'correct_answer'=> $correctAnswer,
                'explanation'   => $qData['explanation'] ?? null,
                'marks'         => $qData['marks'],
                'order'         => $index + 1,
            ]);
            $totalMarks += $qData['marks'];
        }

        $quiz->update(['total_marks' => $totalMarks]);
        return redirect()->route('teacher.quizzes')->with('success', 'Quiz created! Set it to Active when ready.');
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);
        $lessons = Lesson::where('teacher_id', auth()->id())
            ->where('status', 'published')->pluck('title', 'id');
        $quiz->load('questions');
        return view('teacher.quizzes.edit', compact('quiz', 'lessons'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);

        $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'subject'      => ['required', 'string', 'max:255'],
            'class_level'  => ['required', 'string'],
            'lesson_id'    => ['nullable', 'exists:lessons,id'],
            'time_limit'   => ['required', 'integer', 'min:5', 'max:180'],
            'passing_marks'=> ['required', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'questions'    => ['required', 'array', 'min:1'],
            'questions.*.question_text'   => ['required', 'string'],
            'questions.*.type'            => ['required', 'in:mcq,true_false,text'],
            'questions.*.marks'           => ['required', 'integer', 'min:1'],
            'questions.*.correct_answer'  => ['required', 'string'],
            'questions.*.explanation'     => ['nullable', 'string'],
        ]);

        $quiz->update([
            'title'         => $request->title,
            'description'   => $request->description,
            'subject'       => $request->subject,
            'class_level'   => $request->class_level,
            'lesson_id'     => $request->lesson_id,
            'time_limit'    => $request->time_limit,
            'passing_marks' => $request->passing_marks,
            'max_attempts'  => $request->max_attempts,
        ]);

        $existingQuestionIds = [];
        $totalMarks = 0;
        foreach ($request->questions as $index => $qData) {
            $type = $qData['type'];

            $optionA = $optionB = $optionC = $optionD = null;
            $correctAnswer = $qData['correct_answer'];

            if ($type === 'mcq') {
                $optionA = $qData['option_a'] ?? null;
                $optionB = $qData['option_b'] ?? null;
                $optionC = $qData['option_c'] ?? null;
                $optionD = $qData['option_d'] ?? null;
            } elseif ($type === 'true_false') {
                $optionA = 'True';
                $optionB = 'False';
            }

            $questionData = [
                'quiz_id'       => $quiz->id,
                'question_text' => $qData['question_text'],
                'type'          => $type,
                'option_a'      => $optionA,
                'option_b'      => $optionB,
                'option_c'      => $optionC,
                'option_d'      => $optionD,
                'correct_answer'=> $correctAnswer,
                'explanation'   => $qData['explanation'] ?? null,
                'marks'         => $qData['marks'],
                'order'         => $index + 1,
            ];

            if (isset($qData['id']) && $qData['id']) {
                $question = Question::find($qData['id']);
                if ($question && $question->quiz_id === $quiz->id) {
                    $question->update($questionData);
                    $existingQuestionIds[] = $question->id;
                }
            } else {
                $newQuestion = Question::create($questionData);
                $existingQuestionIds[] = $newQuestion->id;
            }
            $totalMarks += $qData['marks'];
        }

        $quiz->questions()->whereNotIn('id', $existingQuestionIds)->delete();
        $quiz->update(['total_marks' => $totalMarks]);

        return redirect()->route('teacher.quizzes')->with('success', 'Quiz updated successfully!');
    }

    public function toggleStatus(Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);

        // Teachers cannot directly activate quizzes — submit for admin review instead.
        if ($quiz->status !== 'active') {
            $quiz->update(['status' => 'pending']);
            \App\Services\AuditLogger::log('submit_quiz_for_review', $quiz, null, ['status' => 'pending']);
            foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
                $admin->notify(new \App\Notifications\ApprovalNotification('Quiz', 'pending', $quiz->title));
            }
            return back()->with('success', 'Quiz submitted for admin review.');
        }

        // If already active, teacher may close the quiz.
        $quiz->update(['status' => 'closed']);
        return back()->with('success', 'Quiz closed.');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);
        $quiz->delete();
        return redirect()->route('teacher.quizzes')->with('success', 'Quiz deleted.');
    }

    public function analytics(Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);
        
        $quiz->load('questions', 'attempts');
        
        $questionStats = [];
        foreach ($quiz->questions as $question) {
            $questionStats[$question->id] = [
                'text' => $question->question_text,
                'correct' => 0,
                'incorrect' => 0,
            ];
        }

        foreach ($quiz->attempts->where('status', 'completed') as $attempt) {
            $answers = $attempt->answers; // already cast to array
            if (is_array($answers)) {
                foreach ($quiz->questions as $question) {
                    if (isset($answers[$question->id])) {
                        if ($answers[$question->id]['is_correct'] ?? false) {
                            $questionStats[$question->id]['correct']++;
                        } else {
                            $questionStats[$question->id]['incorrect']++;
                        }
                    }
                }
            }
        }

        return view('teacher.quizzes.analytics', compact('quiz', 'questionStats'));
    }
}
