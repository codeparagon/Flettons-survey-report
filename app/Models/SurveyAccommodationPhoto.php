<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAccommodationPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'accommodation_assessment_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function accommodationAssessment()
    {
        return $this->belongsTo(SurveyAccommodationAssessment::class, 'accommodation_assessment_id');
    }
}


