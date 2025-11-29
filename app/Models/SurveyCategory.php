<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all subcategories for this category.
     */
    public function subcategories()
    {
        return $this->hasMany(SurveySubcategory::class, 'category_id');
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Get categories with their sections for a specific survey level.
     */
    public static function getCategoriesWithSectionsForLevel($level)
    {
        // Get the level from database
        $surveyLevel = SurveyLevel::where('name', $level)->first();
        
        if (!$surveyLevel) {
            // If level doesn't exist, return empty collection
            return collect();
        }
        
        return $surveyLevel->getCategoriesWithSections();
    }
}