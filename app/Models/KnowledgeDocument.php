<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeDocument extends Model
{
    protected $fillable = [
        'title', 'category', 'content', 'keywords',
        'tfidf_vector', 'created_by', 'is_active',
    ];

    protected $casts = [
        'tfidf_vector' => 'array',
        'is_active'    => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
