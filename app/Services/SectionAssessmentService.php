<?php

namespace App\Services;

use App\Models\SurveySection;
use App\Models\SectionField;
use Illuminate\Http\Request;

class SectionAssessmentService
{
    /**
     * Build validation rules from section fields.
     */
    public function buildValidationRules(SurveySection $section): array
    {
        $rules = [];
        
        // Force reload to get latest fields
        $section->refresh();
        $section->load(['fields' => function($query) {
            $query->where('is_active', true)->orderBy('field_order');
        }]);
        
        $fields = $section->fields;
        
        if (!$fields || $fields->isEmpty()) {
            // Get defects and remaining life options from field_config
            $fieldConfig = $section->field_config ?? [];
            $defectsOptions = $fieldConfig['defects_options'] ?? ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs'];
            $remainingLifeOptions = $fieldConfig['remaining_life_options'] ?? ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs'];
            
            // Return default validation rules for new default fields
            return [
                'report_content' => 'nullable|string',
                'material' => 'nullable|string|max:255',
                'defects' => 'nullable|array',
                'defects.*' => 'nullable|string|in:' . implode(',', $defectsOptions),
                'remaining_life' => 'nullable|string|in:' . implode(',', $remainingLifeOptions),
                'notes' => 'nullable|string|max:2000',
                'photos.*' => 'nullable|image|max:5120',
                'existing_photos' => 'nullable|array',
                'existing_photos.*' => 'nullable|string',
            ];
        }
        
        // Build validation rules for each field
        foreach ($fields as $field) {
            $fieldName = 'field_' . $field->id;
            $rules[$fieldName] = implode('|', $field->getValidationRulesArray());
        }
        
        // Always allow photos and additional_data
        $rules['photos.*'] = 'nullable|image|max:5120';
        $rules['additional_data'] = 'nullable|array';
        
        return $rules;
    }

    /**
     * Format field value based on field type.
     */
    public function formatFieldValue(SectionField $field, $value)
    {
        return $field->formatValue($value);
    }

    /**
     * Extract and validate field values from request.
     */
    public function extractFieldValues(Request $request, SurveySection $section): array
    {
        $fieldValues = [];
        
        // Force reload to get latest fields
        $section->refresh();
        $section->load(['fields' => function($query) {
            $query->where('is_active', true)->orderBy('field_order');
        }]);
        
        $fields = $section->fields;
        
        if (!$fields || $fields->isEmpty()) {
            return [];
        }
        
        foreach ($fields as $field) {
            $fieldName = 'field_' . $field->id;
            $value = $request->input($fieldName);
            
            // Format the value based on field type
            $fieldValues[$field->field_key] = [
                'field_id' => $field->id,
                'field_label' => $field->field_label,
                'field_type' => $field->field_type,
                'value' => $this->formatFieldValue($field, $value),
            ];
        }
        
        return $fieldValues;
    }

    /**
     * Get field values from assessment additional_data.
     */
    public function getFieldValuesFromAssessment($assessment): array
    {
        if (!$assessment || !$assessment->additional_data) {
            return [];
        }
        
        $data = $assessment->additional_data;
        return is_array($data) ? $data : [];
    }

    /**
     * Prepare field values for form display.
     */
    public function prepareFieldValuesForForm(SurveySection $section, $assessment = null): array
    {
        $values = [];
        
        // Force reload to get latest fields
        $section->refresh();
        $section->load(['fields' => function($query) {
            $query->where('is_active', true)->orderBy('field_order');
        }]);
        
        // Get all active fields
        $fields = $section->fields;
        
        if (!$fields || $fields->isEmpty()) {
            return [];
        }
        
        // Get existing values from assessment if available
        $existingValues = [];
        if ($assessment && $assessment->additional_data) {
            $existingData = is_array($assessment->additional_data) ? $assessment->additional_data : [];
            foreach ($existingData as $key => $data) {
                if (isset($data['field_id'])) {
                    $existingValues[$data['field_id']] = $data['value'];
                }
            }
        }
        
        foreach ($fields as $field) {
            $fieldName = 'field_' . $field->id;
            $values[$fieldName] = isset($existingValues[$field->id]) 
                ? $existingValues[$field->id] 
                : ($field->default_value ?? '');
        }
        
        return $values;
    }

    /**
     * Check if section has custom fields configured.
     */
    public function hasCustomFields(SurveySection $section): bool
    {
        // Force reload to get latest fields
        $section->refresh();
        $section->load('fields');
        $fields = $section->fields()->where('is_active', true)->count();
        return $fields > 0;
    }
}

