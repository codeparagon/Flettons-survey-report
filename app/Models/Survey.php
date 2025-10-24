<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        // Signup form (initial details)
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

        // Listing page calculated fields
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

        // Home/billing address
        'inf_field_StreetAddress1',
        'inf_field_PostalCode',

        // Survey property address
        'inf_field_Address2Street1',
        'inf_field_PostalCode2',
        'inf_custom_PropertyLink',

        // Property features & concerns
        'inf_custom_VacantorOccupied',
        'inf_custom_AnyExtensions',
        'inf_custom_Garage',
        'inf_custom_GarageLocation',
        'inf_custom_Garden',
        'inf_custom_GardenLocation',
        'inf_custom_SpecificConcerns',

        // Solicitor details
        'inf_custom_SolicitorFirm',
        'inf_custom_SolicitorFirmName',
        'inf_custom_ConveyancerName',
        'inf_custom_SolicitorPhoneNumber1',
        'inf_custom_SolicitorsEmail',
        'inf_custom_SolicitorAddress',
        'inf_custom_SolicitorPostalCode',

        // Exchange timeline
        'inf_custom_exchange_known',
        'inf_custom_ExchangeDate',

        // Estate agent details
        'inf_custom_AgentCompanyName',
        'inf_custom_AgentName',
        'inf_custom_AgentPhoneNumber',
        'inf_custom_AgentsEmail',
        'inf_field_Address3Street1',
        'inf_field_PostalCode3',

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

    /**
     * Get all section assessments for this survey.
     */
    public function sectionAssessments()
    {
        return $this->hasMany(SurveySectionAssessment::class);
    }

    /**
     * Get sections required for this survey's level.
     */
    public function getRequiredSections()
    {
        return SurveySection::getSectionsForLevel($this->level);
    }

    /**
     * Get completion progress for this survey.
     */
    public function getCompletionProgress()
    {
        $requiredSections = $this->getRequiredSections();
        $completedAssessments = $this->sectionAssessments()->completed()->count();
        $totalSections = $requiredSections->count();
        
        return [
            'completed' => $completedAssessments,
            'total' => $totalSections,
            'percentage' => $totalSections > 0 ? round(($completedAssessments / $totalSections) * 100) : 0,
        ];
    }

    /**
     * Check if survey is fully completed.
     */
    public function isFullyCompleted()
    {
        $progress = $this->getCompletionProgress();
        return $progress['completed'] === $progress['total'];
    }

    /**
     * Get assessment for a specific section.
     */
    public function getAssessmentForSection($sectionName)
    {
        return $this->sectionAssessments()
            ->whereHas('section', function($query) use ($sectionName) {
                $query->where('name', $sectionName);
            })
            ->first();
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge-warning',
            'assigned' => 'badge-info',
            'in_progress' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];
        return $badges[$this->status] ?? 'badge-secondary';
    }

    /**
     * Get client name from form data.
     */
    public function getClientNameAttribute()
    {
        return trim(($this->inf_field_FirstName ?? $this->first_name) . ' ' . ($this->inf_field_LastName ?? $this->last_name));
    }

    /**
     * Get client email from form data.
     */
    public function getClientEmailAttribute()
    {
        return $this->inf_field_Email ?? $this->email_address;
    }

    /**
     * Get property address from form data.
     */
    public function getPropertyAddressFullAttribute()
    {
        return $this->inf_field_Address2Street1 ?? $this->full_address;
    }
}
