<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAccommodationType extends Model
{
    use HasFactory;

    protected $table = 'survey_accommodation_types';

    protected $fillable = [
        'key_name',
        'display_name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function assessments()
    {
        return $this->hasMany(SurveyAccommodationAssessment::class, 'accommodation_type_id');
    }

    public function components()
    {
        return $this->belongsToMany(
            SurveyAccommodationComponent::class,
            'survey_accommodation_type_components',
            'accommodation_type_id',
            'component_id'
        )
        ->withPivot('is_required', 'sort_order')
        ->withTimestamps()
        ->orderBy('survey_accommodation_type_components.sort_order');
    }
}


