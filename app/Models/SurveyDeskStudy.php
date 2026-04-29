<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyDeskStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'address',
        'job_reference',
        'longitude',
        'latitude',
        'map_image_path',
        'flood_risks',
        'planning',
    ];

    protected $casts = [
        'flood_risks' => 'array',
        'planning' => 'array',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}

