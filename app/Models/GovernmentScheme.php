<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Translatable;

class GovernmentScheme extends Model
{
    use Translatable;

    protected $fillable = [
        'title', 'title_hi', 'title_pa',
        'description', 'description_hi', 'description_pa',
        'target_audience', 'target_audience_hi', 'target_audience_pa',
        'benefits', 'benefits_hi', 'benefits_pa',
        'url'
    ];
}
