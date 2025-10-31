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
        'generation_method',
        'field_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'field_config' => 'array',
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
     * Get all fields for this section.
     */
    public function fields()
    {
        return $this->hasMany(SectionField::class, 'survey_section_id');
    }

    /**
     * Get active fields ordered by field_order.
     */
    public function getActiveFields()
    {
        // Ensure we refresh the relationship to get latest data
        $this->load('fields');
        return $this->fields()->where('is_active', true)->orderBy('field_order')->orderBy('field_label')->get();
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

    /**
     * Get all levels that this section belongs to.
     */
    public function levels()
    {
        return $this->belongsToMany(SurveyLevel::class, 'survey_level_sections', 'survey_section_id', 'survey_level_id')
                    ->withPivot('sort_order')
                    ->orderBy('survey_level_sections.sort_order');
    }

    /**
     * Scope for database-driven sections.
     */
    public function scopeDatabaseDriven($query)
    {
        return $query->where('generation_method', 'database');
    }

    /**
     * Scope for AI-generated sections.
     */
    public function scopeAiGenerated($query)
    {
        return $query->where('generation_method', 'ai');
    }

    /**
     * Scope for hybrid sections.
     */
    public function scopeHybrid($query)
    {
        return $query->where('generation_method', 'hybrid');
    }

    /**
     * Get field configurations ordered by field_order.
     */
    public function getFieldConfigurations()
    {
        return $this->fields()->active()->ordered()->get();
    }
}