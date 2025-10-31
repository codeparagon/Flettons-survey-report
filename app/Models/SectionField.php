<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionField extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_section_id',
        'field_key',
        'field_label',
        'field_type',
        'field_order',
        'is_required',
        'validation_rules',
        'options',
        'default_value',
        'help_text',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'field_order' => 'integer',
        'validation_rules' => 'array',
        'options' => 'array',
    ];

    /**
     * Get the section this field belongs to.
     */
    public function section()
    {
        return $this->belongsTo(SurveySection::class, 'survey_section_id');
    }

    /**
     * Scope for active fields.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered fields.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('field_order')->orderBy('field_label');
    }

    /**
     * Get Laravel validation rules array.
     */
    public function getValidationRulesArray()
    {
        $rules = [];
        
        // Add base rules based on field type
        switch ($this->field_type) {
            case 'textarea':
            case 'single-text':
                $rules[] = 'string';
                if ($this->validation_rules && isset($this->validation_rules['max'])) {
                    $rules[] = 'max:' . $this->validation_rules['max'];
                }
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'numeric':
                $rules[] = 'numeric';
                if ($this->validation_rules && isset($this->validation_rules['min'])) {
                    $rules[] = 'min:' . $this->validation_rules['min'];
                }
                if ($this->validation_rules && isset($this->validation_rules['max'])) {
                    $rules[] = 'max:' . $this->validation_rules['max'];
                }
                break;
            case 'dropdown':
                if ($this->options && is_array($this->options)) {
                    $rules[] = 'in:' . implode(',', $this->options);
                } else {
                    $rules[] = 'string';
                }
                break;
            case 'rating':
                $rules[] = 'in:excellent,good,fair,poor';
                break;
        }
        
        // Add required rule
        if ($this->is_required) {
            array_unshift($rules, 'required');
        } else {
            $rules[] = 'nullable';
        }
        
        // Merge custom validation rules if any
        if ($this->validation_rules && is_array($this->validation_rules)) {
            $customRules = array_filter($this->validation_rules, function($key) {
                return !in_array($key, ['min', 'max']);
            }, ARRAY_FILTER_USE_KEY);
            
            if (!empty($customRules)) {
                $rules = array_merge($rules, array_values($customRules));
            }
        }
        
        return $rules;
    }

    /**
     * Format value based on field type.
     */
    public function formatValue($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        switch ($this->field_type) {
            case 'numeric':
                return is_numeric($value) ? (float)$value : null;
            case 'date':
                return $value; // Keep as string, will be formatted in view
            default:
                return (string)$value;
        }
    }
}
