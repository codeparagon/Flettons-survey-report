<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all section definitions for this level.
     */
    public function sectionDefinitions()
    {
        return $this->belongsToMany(
            SurveySectionDefinition::class, 
            'survey_level_section_definitions', 
            'survey_level_id', 
            'section_definition_id'
        )->withPivot('sort_order')
         ->orderBy('survey_level_section_definitions.sort_order');
    }

    /**
     * Get all surveys for this level.
     */
    public function surveys()
    {
        return $this->hasMany(Survey::class, 'level', 'name');
    }

    /**
     * Scope for active levels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered levels.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Get categories with their sections for this level.
     */
    public function getCategoriesWithSections()
    {
        $sectionIds = $this->sectionDefinitions->pluck('id');
        
        return SurveyCategory::active()
            ->ordered()
            ->with(['subcategories' => function($query) use ($sectionIds) {
                $query->active()->ordered()->with(['sectionDefinitions' => function($q) use ($sectionIds) {
                    $q->whereIn('id', $sectionIds)
                      ->where('is_active', true)
                      ->orderBy('sort_order');
                }]);
            }])
            ->get();
    }

    /**
     * Get section definition names for this level.
     */
    public function getSectionNames()
    {
        return $this->sectionDefinitions->pluck('name')->toArray();
    }
}
