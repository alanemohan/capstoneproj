<?php

namespace App\Observers;

use App\Jobs\TranslateContentJob;
use Illuminate\Database\Eloquent\Model;

class ContentTranslationObserver
{
    /**
     * Fields that should be auto-translated per model class.
     */
    protected array $fieldMap = [
        \App\Models\Announcement::class => ['title', 'content'],
        \App\Models\Course::class       => ['title', 'description'],
        \App\Models\Lesson::class       => ['title', 'description'],
        \App\Models\Quiz::class         => ['title', 'description'],
        \App\Models\Question::class     => ['question_text', 'explanation'],
        \App\Models\LiveClass::class    => ['title', 'description'],
    ];

    /**
     * Fired after a model is created or updated.
     * Dispatches a translation job if English source fields changed.
     */
    public function saved(Model $model): void
    {
        $modelClass = get_class($model);
        $fields     = $this->fieldMap[$modelClass] ?? null;

        if (!$fields) {
            return;
        }

        // Only dispatch if any source field was actually changed
        $changed = false;
        foreach ($fields as $field) {
            if ($model->wasChanged($field) || $model->wasRecentlyCreated) {
                $changed = true;
                break;
            }
        }

        if (!$changed) {
            return;
        }

        // Mark translation as pending (non-observer update to avoid re-triggering)
        if (in_array('translation_pending', $model->getFillable())) {
            $modelClass::whereKey($model->id)->update(['translation_pending' => true]);
        }

        // Dispatch job (sync queue = runs immediately, no worker needed)
        TranslateContentJob::dispatch($modelClass, $model->id, $fields);
    }
}
