<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyContentSection extends Model
{
    use HasFactory;

    protected $table = 'survey_content_sections';

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'subcategory_id',
        'tags',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the category that this content section belongs to (optional).
     */
    public function category()
    {
        return $this->belongsTo(SurveyCategory::class, 'category_id');
    }

    /**
     * Get the subcategory that this content section belongs to (optional).
     */
    public function subcategory()
    {
        return $this->belongsTo(SurveySubcategory::class, 'subcategory_id');
    }

    /**
     * Scope for active content sections.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered content sections.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Scope for standalone sections (not linked to category/subcategory).
     */
    public function scopeStandalone($query)
    {
        return $query->whereNull('category_id')->whereNull('subcategory_id');
    }

    /**
     * Scope for sections linked to a category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId)->whereNull('subcategory_id');
    }

    /**
     * Scope for sections linked to a subcategory.
     */
    public function scopeBySubcategory($query, $subcategoryId)
    {
        return $query->where('subcategory_id', $subcategoryId);
    }
}
