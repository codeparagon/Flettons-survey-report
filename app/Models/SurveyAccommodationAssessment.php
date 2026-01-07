<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAccommodationAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'accommodation_type_id',
        'custom_name',
        'clone_index',
        'notes',
        'condition_rating',
        'report_content',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'clone_index' => 'integer',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function accommodationType()
    {
        return $this->belongsTo(SurveyAccommodationType::class, 'accommodation_type_id');
    }

    public function componentAssessments()
    {
        return $this->hasMany(SurveyAccommodationComponentAssessment::class, 'accommodation_assessment_id');
    }

    public function photos()
    {
        return $this->hasMany(SurveyAccommodationPhoto::class, 'accommodation_assessment_id');
    }
}

