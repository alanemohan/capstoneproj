<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = [
        'teacher_id', 'course_id', 'title', 'description', 
        'meeting_link', 'scheduled_at', 'duration_minutes', 'status'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
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
