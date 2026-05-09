<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['teacher_id', 'batch_id', 'title', 'description', 'due_date', 'max_marks'];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
