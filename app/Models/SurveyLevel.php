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
     * Get all sections for this level.
     */
    public function sections()
    {
        return $this->belongsToMany(SurveySection::class, 'survey_level_sections', 'survey_level_id', 'survey_section_id')
                    ->withPivot('sort_order')
                    ->orderBy('survey_level_sections.sort_order');
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
        $sectionIds = $this->sections->pluck('id');
        
        return SurveyCategory::active()
            ->ordered()
            ->with(['sections' => function($query) use ($sectionIds) {
                $query->whereIn('id', $sectionIds)
                      ->where('is_active', true)
                      ->orderBy('sort_order');
            }])
            ->get();
    }

    /**
     * Get section names for this level.
     */
    public function getSectionNames()
    {
        return $this->sections->pluck('name')->toArray();
    }
}