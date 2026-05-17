<?php

namespace App\Jobs;

use App\Services\TranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    protected string $modelClass;
    protected int $modelId;
    protected array $fields; // e.g. ['title', 'content']

    public function __construct(string $modelClass, int $modelId, array $fields)
    {
        $this->modelClass = $modelClass;
        $this->modelId    = $modelId;
        $this->fields     = $fields;
    }

    public function handle(TranslationService $translator): void
    {
        $model = ($this->modelClass)::find($this->modelId);

        if (!$model) {
            return;
        }

        $locales = ['hi', 'pa'];
        $updates = ['translation_pending' => false];

        foreach ($locales as $locale) {
            foreach ($this->fields as $field) {
                $sourceValue = $model->{$field} ?? null;
                $targetColumn = "{$field}_{$locale}";

                // Skip if already translated (unless source changed, handled by observer)
                if (empty($sourceValue)) {
                    continue;
                }

                // Only re-translate if the target is empty or model method says so
                if (method_exists($model, 'shouldRetranslate') && !$model->shouldRetranslate($field, $locale)) {
                    // Check if column is empty before skipping
                    if (!empty($model->{$targetColumn})) {
                        continue;
                    }
                }

                $translated = $translator->translateText($sourceValue, $locale);

                if (!empty($translated) && $translated !== $sourceValue) {
                    $updates[$targetColumn] = $translated;
                }
            }
        }

        if (count($updates) > 1) { // more than just translation_pending
            // Use whereKey to avoid firing observer again
            ($this->modelClass)::whereKey($this->modelId)->update($updates);
            Log::info("TranslateContentJob: translated {$this->modelClass}#{$this->modelId}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("TranslateContentJob failed for {$this->modelClass}#{$this->modelId}: " . $exception->getMessage());

        // Mark translation as not pending even on failure
        ($this->modelClass)::whereKey($this->modelId)->update(['translation_pending' => false]);
    }
}
