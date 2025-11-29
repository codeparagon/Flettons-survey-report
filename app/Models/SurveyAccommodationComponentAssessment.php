<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAccommodationComponentAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'accommodation_assessment_id',
        'component_id',
        'material_id',
    ];

    public function accommodationAssessment()
    {
        return $this->belongsTo(SurveyAccommodationAssessment::class, 'accommodation_assessment_id');
    }

    public function component()
    {
        return $this->belongsTo(SurveyAccommodationComponent::class, 'component_id');
    }

    public function material()
    {
        return $this->belongsTo(SurveyAccommodationOption::class, 'material_id');
    }

    public function defects()
    {
        return $this->belongsToMany(
            SurveyAccommodationOption::class,
            'survey_accommodation_component_defects',
            'component_assessment_id',
            'defect_option_id'
        );
    }
}

