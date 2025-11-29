<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySectionAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'section_definition_id',
        'condition_rating',
        'section_type_id',
        'location_id',
        'structure_id',
        'material_id',
        'remaining_life_id',
        'notes',
        'report_content',
        'is_clone',
        'cloned_from_id',
        'clone_index',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_clone' => 'boolean',
        'completed_at' => 'datetime',
        'condition_rating' => 'integer',
        'clone_index' => 'integer',
    ];

    /**
     * Get the survey this assessment belongs to.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the section definition this assessment is for.
     */
    public function sectionDefinition()
    {
        return $this->belongsTo(SurveySectionDefinition::class, 'section_definition_id');
    }

    /**
     * Get the section type option.
     */
    public function sectionType()
    {
        return $this->belongsTo(SurveyOption::class, 'section_type_id');
    }

    /**
     * Get the location option.
     */
    public function location()
    {
        return $this->belongsTo(SurveyOption::class, 'location_id');
    }

    /**
     * Get the structure option.
     */
    public function structure()
    {
        return $this->belongsTo(SurveyOption::class, 'structure_id');
    }

    /**
     * Get the material option.
     */
    public function material()
    {
        return $this->belongsTo(SurveyOption::class, 'material_id');
    }

    /**
     * Get the remaining life option.
     */
    public function remainingLife()
    {
        return $this->belongsTo(SurveyOption::class, 'remaining_life_id');
    }

    /**
     * Get all defects for this assessment.
     */
    public function defects()
    {
        return $this->belongsToMany(SurveyOption::class, 'survey_section_defects', 'section_assessment_id', 'defect_option_id');
    }

    /**
     * Get all photos for this assessment.
     */
    public function photos()
    {
        return $this->hasMany(SurveySectionPhoto::class, 'section_assessment_id')->orderBy('sort_order');
    }

    /**
     * Get all costs for this assessment.
     */
    public function costs()
    {
        return $this->hasMany(SurveySectionCost::class, 'section_assessment_id');
    }

    /**
     * Get the assessment this was cloned from.
     */
    public function clonedFrom()
    {
        return $this->belongsTo(SurveySectionAssessment::class, 'cloned_from_id');
    }

    /**
     * Get all clones of this assessment.
     */
    public function clones()
    {
        return $this->hasMany(SurveySectionAssessment::class, 'cloned_from_id');
    }

    /**
     * Get the user who completed this assessment.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get condition rating badge class.
     */
    public function getConditionBadgeAttribute()
    {
        $badges = [
            'excellent' => 'badge-success',
            'good' => 'badge-info',
            'fair' => 'badge-warning',
            'poor' => 'badge-danger',
        ];
        return $badges[$this->condition_rating] ?? 'badge-secondary';
    }

    /**
     * Get completion status badge.
     */
    public function getCompletionBadgeAttribute()
    {
        return $this->is_completed ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Check if assessment has report content.
     */
    public function hasReportContent()
    {
        return !empty(trim($this->report_content ?? ''));
    }

    /**
     * Get formatted completion date.
     */
    public function getFormattedCompletedAtAttribute()
    {
        return $this->completed_at ? $this->completed_at->format('M d, Y H:i') : null;
    }

    /**
     * Scope for completed assessments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope for incomplete assessments.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }
}