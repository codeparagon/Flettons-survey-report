<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        // Client information
        'first_name',
        'last_name',
        'email_address',
        'telephone_number',
        'full_address',
        'postcode',
        'house_or_flat',
        'number_of_bedrooms',
        'market_value',
        'listed_building',
        'over1650',
        'sqft_area',

        // Level and pricing
        'level',
        'breakdown',
        'aerial',
        'insurance',
        'level_total',
        'addons',

        // Client identity & contact
        'inf_field_Title',
        'inf_field_FirstName',
        'inf_field_LastName',
        'inf_field_Email',
        'inf_field_Phone1',
        'inf_field_Phone2',
        'inf_field_Address1Street1',
        'inf_field_Address1Street2',
        'inf_field_City',
        'inf_field_State',
        'inf_field_Country',
        'inf_field_PostalCode',

        // Additional client fields
        'inf_field_Address2Street1',
        'inf_field_Address2Street2',
        'inf_field_City2',
        'inf_field_State2',
        'inf_field_Country2',
        'inf_field_PostalCode2',
        'inf_field_Address3Street1',
        'inf_field_PostalCode3',

        // Agent information
        'inf_custom_AgentsName',
        'inf_custom_AgentsEmail',

        // Pricing
        'level1_price',
        'level2_price',
        'level3_price',
        'level4_price',

        // Payment URLs
        'level1_payment_url',
        'level2_payment_url',
        'level3_payment_url',
        'level4_payment_url',

        // Acceptance & signature
        'inf_option_IconfirmthatIhavereadandunderstandtheterms',
        'inf_custom_infcustomSignature',
        'contact_id',

        'quote_summary_page',
        'current_step',
        'is_submitted',
        
        // Internal management fields
        'surveyor_id',
        'status',
        'payment_status',
        'scheduled_date',
        'admin_notes',

        // extra fields
        'receptions',
        'bathrooms',
        'job_reference',
        'access_contact',
        'access_role',
        'client_concerns', 
    ];

    protected $casts = [
        'market_value' => 'integer',
        'sqft_area' => 'integer',
        'number_of_bedrooms' => 'integer',
        'breakdown' => 'boolean',
        'aerial' => 'boolean',
        'insurance' => 'boolean',
        'addons' => 'boolean',
        'level_total' => 'decimal:2',
        'level1_price' => 'decimal:2',
        'level2_price' => 'decimal:2',
        'level3_price' => 'decimal:2',
        'level4_price' => 'decimal:2',
        'current_step' => 'integer',
        'scheduled_date' => 'date',
    ];

    /**
     * Get the surveyor assigned to this survey.
     */
    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }
    
    public function notes()
    {
        return $this->hasMany(SurveyNote::class, 'survey_id');
    }

    /**
     * Get all section assessments for this survey.
     */
    public function sectionAssessments()
    {
        return $this->hasMany(SurveySectionAssessment::class);
    }

    /**
     * Get the survey level.
     */
    public function surveyLevel()
    {
        return $this->belongsTo(SurveyLevel::class, 'level', 'name');
    }

    /**
     * Get section definitions required for this survey's level.
     */
    public function getRequiredSectionDefinitions()
    {
        $surveyLevel = SurveyLevel::where('name', $this->level)->first();
        
        if (!$surveyLevel) {
            return collect();
        }
        
        return $surveyLevel->sectionDefinitions;
    }

    /**
     * Get completion progress for this survey.
     */
    public function getCompletionProgress()
    {
        $requiredSections = $this->getRequiredSectionDefinitions();
        $totalSections = $requiredSections->count();
        
        if ($totalSections === 0) {
            return ['percentage' => 0, 'completed' => 0, 'total' => 0];
        }
        
        $completedSections = $this->sectionAssessments()
            ->where('is_completed', true)
            ->whereIn('section_definition_id', $requiredSections->pluck('id'))
            ->count();
        
        $percentage = ($completedSections / $totalSections) * 100;
        
        return [
            'percentage' => round($percentage, 1),
            'completed' => $completedSections,
            'total' => $totalSections,
        ];
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * Get status badge class for styling.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge-secondary',
            'assigned' => 'badge-info',
            'in_progress' => 'badge-warning',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];
        
        return $badges[$this->status] ?? 'badge-secondary';
    }

    /**
     * Get client full name.
     */
    public function getClientNameAttribute()
    {
        if ($this->inf_field_FirstName && $this->inf_field_LastName) {
            return trim($this->inf_field_FirstName . ' ' . $this->inf_field_LastName);
        }
        if ($this->first_name && $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->first_name ?? $this->inf_field_FirstName ?? 'Unknown Client';
    }

    /**
     * Get client email.
     */
    public function getClientEmailAttribute()
    {
        return $this->inf_field_Email ?? $this->email_address ?? null;
    }

    /**
     * Get full property address.
     */
    public function getPropertyAddressFullAttribute()
    {
        $parts = [];
        
        if ($this->inf_field_Address1Street1) {
            $parts[] = $this->inf_field_Address1Street1;
            if ($this->inf_field_Address1Street2) {
                $parts[] = $this->inf_field_Address1Street2;
            }
        }
        
        if ($this->inf_field_City) {
            $parts[] = $this->inf_field_City;
        }
        
        if ($this->inf_field_State) {
            $parts[] = $this->inf_field_State;
        }
        
        if ($this->inf_field_PostalCode) {
            $parts[] = $this->inf_field_PostalCode;
        }
        
        if (empty($parts) && $this->full_address) {
            return $this->full_address;
        }
        
        return !empty($parts) ? implode(', ', $parts) : 'Address not provided';
    }

    /**
     * Get assessments with report content (for report generation).
     */
    public function getAssessmentsWithReportContent()
    {
        return $this->sectionAssessments()
            ->with('sectionDefinition.subcategory.category')
            ->get()
            ->filter(function($assessment) {
                return $assessment->hasReportContent();
            });
    }

    /**
     * Get assessments grouped by category with report content.
     */
    public function getAssessmentsByCategoryWithContent()
    {
        $assessments = $this->getAssessmentsWithReportContent();
        
        return $assessments->groupBy(function($assessment) {
            return $assessment->sectionDefinition->subcategory->category->display_name ?? 'Uncategorized';
        });
    }
}
