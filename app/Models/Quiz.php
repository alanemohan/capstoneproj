<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id', 'lesson_id', 'title', 'description',
        'subject', 'class_level', 'time_limit', 'passing_marks',
        'total_marks', 'status', 'max_attempts',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_marks' => 'integer',
        'total_marks' => 'integer',
        'max_attempts' => 'integer',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Quiz $quiz): void {
            // Prevent non-admins from activating quizzes directly
            if ($quiz->isDirty('status') && $quiz->status === 'active') {
                if (!auth()->check() || auth()->user()->role !== 'admin') {
                    $quiz->status = 'pending';
                    $quiz->approved_by = null;
                    $quiz->approved_at = null;
                }
            }
        });
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function studentAttempts(int $studentId)
    {
        return $this->hasMany(QuizAttempt::class)->where('student_id', $studentId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function recalculateTotalMarks(): void
    {
        $this->update(['total_marks' => $this->questions()->sum('marks')]);
    }

    public function canAttempt(int $studentId): bool
    {
        $attemptCount = $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->count();
        return $attemptCount < $this->max_attempts;
    }
}
