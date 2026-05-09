<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Translatable;

class Scholarship extends Model
{
    use Translatable;

    protected $fillable = [
        'title', 'title_hi', 'title_pa',
        'description', 'description_hi', 'description_pa',
        'eligibility_criteria', 'eligibility_criteria_hi', 'eligibility_criteria_pa',
        'deadline',
        'amount', 'amount_hi', 'amount_pa',
        'url'
    ];
}
