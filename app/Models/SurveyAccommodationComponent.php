<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAccommodationComponent extends Model
{
    use HasFactory;

    protected $table = 'survey_accommodation_components';

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

    public function componentAssessments()
    {
        return $this->hasMany(SurveyAccommodationComponentAssessment::class, 'component_id');
    }
}


