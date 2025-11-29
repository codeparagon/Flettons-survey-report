<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySectionCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_assessment_id',
        'category',
        'description',
        'due_year',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_year' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function sectionAssessment()
    {
        return $this->belongsTo(SurveySectionAssessment::class, 'section_assessment_id');
    }
}


