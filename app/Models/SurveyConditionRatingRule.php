<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyConditionRatingRule extends Model
{
    use HasFactory;

    protected $table = 'survey_condition_rating_rules';

    protected $fillable = [
        'option_type',
        'option_value',
        'condition_rating',
    ];

    protected $casts = [
        'condition_rating' => 'integer',
    ];
}

