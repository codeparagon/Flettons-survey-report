<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'survey_category_id',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the category that this section belongs to.
     */
    public function category()
    {
        return $this->belongsTo(SurveyCategory::class, 'survey_category_id');
    }

    /**
     * Get all assessments for this section.
     */
    public function assessments()
    {
        return $this->hasMany(SurveySectionAssessment::class);
    }

    /**
     * Scope for active sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered sections.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Get sections required for a survey level.
     */
    public static function getSectionsForLevel($level)
    {
        $surveyLevel = \App\Models\SurveyLevel::where('name', $level)->first();
        
        if (!$surveyLevel) {
            return collect();
        }
        
        return $surveyLevel->sections;
    }

    /**
     * Get sections grouped by category for a survey level.
     */
    public static function getSectionsByCategoryForLevel($level)
    {
        $surveyLevel = \App\Models\SurveyLevel::where('name', $level)->first();
        
        if (!$surveyLevel) {
            return collect();
        }
        
        return $surveyLevel->sections->groupBy('category.display_name');
    }
}