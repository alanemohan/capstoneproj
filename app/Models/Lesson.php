<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'subject',
        'class_level',
        'language',
        'file_path',
        'file_type',
        'content',
        'thumbnail',
        'duration_minutes',
        'status',
        'view_count',
        'download_count',
        'approved_by',
        'approved_at',
        'course_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'view_count' => 'integer',
        'download_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Lesson $lesson): void {
            // Prevent non-admins from publishing lessons directly
            if ($lesson->isDirty('status') && $lesson->status === 'published') {
                if (!auth()->check() || auth()->user()->role !== 'admin') {
                    $lesson->status = 'pending';
                    $lesson->approved_by = null;
                    $lesson->approved_at = null;
                }
            }
        });
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function contents()
    {
        return $this->hasMany(LessonContent::class)->orderBy('order');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return Storage::disk('public')->url($this->thumbnail);
        }
        return asset('images/lesson-placeholder.png');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForClass($query, string $classLevel)
    {
        return $query->where('class_level', $classLevel);
    }

    public function scopeForSubject($query, string $subject)
    {
        return $query->where('subject', $subject);
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}
