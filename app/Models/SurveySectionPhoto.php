<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySectionPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_assessment_id',
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

    public function sectionAssessment()
    {
        return $this->belongsTo(SurveySectionAssessment::class, 'section_assessment_id');
    }
}


