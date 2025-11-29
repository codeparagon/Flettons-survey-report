<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySectionDefinition extends Model
{
    use HasFactory;

    protected $table = 'survey_section_definitions';

    protected $fillable = [
        'subcategory_id',
        'name',
        'display_name',
        'is_clonable',
        'max_clones',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_clonable' => 'boolean',
        'is_active' => 'boolean',
        'max_clones' => 'integer',
        'sort_order' => 'integer',
    ];

    public function subcategory()
    {
        return $this->belongsTo(SurveySubcategory::class, 'subcategory_id');
    }

    public function assessments()
    {
        return $this->hasMany(SurveySectionAssessment::class, 'section_definition_id');
    }

    public function requiredFields()
    {
        return $this->belongsToMany(
            SurveyOptionType::class,
            'survey_section_required_fields',
            'section_definition_id',
            'option_type_id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }
}


