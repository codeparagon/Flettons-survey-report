<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAccommodationComponentSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'accommodation_type_id',
        'component_id',
        'content',
        'input_hash',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function accommodationType()
    {
        return $this->belongsTo(SurveyAccommodationType::class, 'accommodation_type_id');
    }

    public function component()
    {
        return $this->belongsTo(SurveyAccommodationComponent::class, 'component_id');
    }
}

