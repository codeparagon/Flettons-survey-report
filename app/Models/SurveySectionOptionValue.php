<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveySectionOptionValue extends Model
{
    protected $fillable = [
        'section_assessment_id',
        'option_type_id',
        'option_id',
    ];

    public function sectionAssessment(): BelongsTo
    {
        return $this->belongsTo(SurveySectionAssessment::class, 'section_assessment_id');
    }

    public function optionType(): BelongsTo
    {
        return $this->belongsTo(SurveyOptionType::class, 'option_type_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SurveyOption::class, 'option_id');
    }
}
