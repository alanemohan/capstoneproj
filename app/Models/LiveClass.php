<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    use HasTranslations;

    protected $fillable = [
        'teacher_id', 'course_id', 
        'title', 'title_hi', 'title_pa',
        'description', 'description_hi', 'description_pa', 
        'meeting_link', 'scheduled_at', 'duration_minutes', 'status',
        'translation_pending'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
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
