<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentReview extends Model
{
    protected $fillable = ['admin_id', 'content_type', 'content_id', 'action', 'notes'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function content()
    {
        return $this->morphTo();
    }
}
