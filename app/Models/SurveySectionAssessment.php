<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveySectionAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'survey_section_id',
        'condition_rating',
        'defects_noted',
        'recommendations',
        'notes',
        'is_completed',
        'completed_at',
        'completed_by',
        'photos',
        'additional_data',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'photos' => 'array',
        'additional_data' => 'array',
    ];

    /**
     * Get the survey this assessment belongs to.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the section this assessment is for.
     */
    public function section()
    {
        return $this->belongsTo(SurveySection::class, 'survey_section_id');
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