<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Jobs\TranslateContentJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    public function index()
    {
        $stats = [
            'announcements' => [
                'total' => Announcement::count(),
                'pending' => Announcement::where('translation_pending', true)->count(),
                'model' => Announcement::class,
                'fields' => ['title', 'content'],
            ],
            'courses' => [
                'total' => Course::count(),
                'pending' => Course::where('translation_pending', true)->count(),
                'model' => Course::class,
                'fields' => ['title', 'description'],
            ],
            'lessons' => [
                'total' => Lesson::count(),
                'pending' => Lesson::where('translation_pending', true)->count(),
                'model' => Lesson::class,
                'fields' => ['title', 'description'],
            ],
            'quizzes' => [
                'total' => Quiz::count(),
                'pending' => Quiz::where('translation_pending', true)->count(),
                'model' => Quiz::class,
                'fields' => ['title', 'description'],
            ],
        ];

        return view('admin.translations.index', compact('stats'));
    }

    public function retranslate(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'id' => 'required|integer',
            'fields' => 'required|array',
        ]);

        TranslateContentJob::dispatch($request->model, $request->id, $request->fields);

        return back()->with('success', 'Translation job dispatched successfully.');
    }

    public function bulkRetranslate(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'fields' => 'required|array',
        ]);

        $items = ($request->model)::all();
        foreach ($items as $item) {
            TranslateContentJob::dispatch($request->model, $item->id, $request->fields);
        }

        return back()->with('success', 'Bulk translation jobs dispatched successfully.');
    }

    public function update(Request $request, $modelType, $id)
    {
        $modelClass = $this->getModelClass($modelType);
        if (!$modelClass) {
            return back()->with('error', 'Invalid model type.');
        }

        $model = $modelClass::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        
        $model->update($data);

        return back()->with('success', 'Translations updated manually.');
    }

    protected function getModelClass($type)
    {
        $map = [
            'announcement' => Announcement::class,
            'course' => Course::class,
            'lesson' => Lesson::class,
            'quiz' => Quiz::class,
            'question' => Question::class,
        ];

        return $map[$type] ?? null;
    }
}
