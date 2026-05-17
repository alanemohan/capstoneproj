<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'quiz_id',
        'question_text', 'question_text_hi', 'question_text_pa',
        'type',
        'option_a', 'option_b', 'option_c', 'option_d',
        'options_hi', 'options_pa',
        'correct_answer',
        'explanation', 'explanation_hi', 'explanation_pa',
        'marks', 'order',
    ];

    protected $casts = [
        'marks'      => 'integer',
        'order'      => 'integer',
        'options_hi' => 'array',
        'options_pa' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function isCorrect(string $answer): bool
    {
        if ($this->type === 'text') {
            return mb_strtolower(trim($answer)) === mb_strtolower(trim($this->correct_answer));
        }
        return strtolower($answer) === strtolower($this->correct_answer);
    }

    public function getOptionAttribute(string $key): ?string
    {
        return match(strtolower($key)) {
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
            default => null,
        };
    }

    /**
     * Get localized option text for MCQ options.
     * Options stored as ['a' => 'text', 'b' => 'text', ...]
     */
    public function getLocalizedOption(string $key): string
    {
        $locale = app()->getLocale();
        $keyLower = strtolower($key);

        if ($locale !== 'en') {
            $optionsLocale = $this->{"options_{$locale}"};
            if (is_array($optionsLocale) && !empty($optionsLocale[$keyLower])) {
                return $optionsLocale[$keyLower];
            }
        }

        return $this->getOptionAttribute($keyLower) ?? '';
    }

    public function getCorrectAnswerTextAttribute(): string
    {
        if ($this->type === 'text') {
            return $this->correct_answer;
        }
        return $this->getOptionAttribute($this->correct_answer) ?? '';
    }

    public function getOptionsForDisplay(): array
    {
        return match($this->type) {
            'true_false' => ['a' => 'True', 'b' => 'False'],
            'mcq'        => array_filter([
                'a' => $this->option_a,
                'b' => $this->option_b,
                'c' => $this->option_c,
                'd' => $this->option_d,
            ]),
            default => [],
        };
    }
}
