<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasTranslations;

    protected $fillable = [
        'teacher_id', 'course_id',
        'title', 'title_hi', 'title_pa',
        'content', 'content_hi', 'content_pa',
        'target_class', 'translation_pending',
    ];

    protected $casts = [
        'translation_pending' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
