<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveySection;
use App\Models\SurveyCategory;
use App\Models\SectionField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveySectionController extends Controller
{
    /**
     * Display a listing of survey sections.
     */
    public function index()
    {
        $sections = SurveySection::with(['category', 'fields'])->ordered()->get();
        
        return view('admin.survey-sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new survey section.
     */
    public function create()
    {
        $categories = SurveyCategory::active()->ordered()->get();
        $levels = \App\Models\SurveyLevel::active()->ordered()->get();
        
        return view('admin.survey-sections.create', compact('categories', 'levels'));
    }

    /**
     * Store a newly created survey section.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_sections,name',
            'display_name' => 'required|string|max:255',
            'survey_category_id' => 'required|exists:survey_categories,id',
            'icon' => 'nullable|string|max:255',
            'custom_icon' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|max:2048', // 2MB max
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'generation_method' => 'nullable|in:database,ai',
            'field_config' => 'nullable|array',
            'field_config.ai_prompt_template' => 'nullable|string',
            'field_config.defects_options' => 'nullable|array',
            'field_config.defects_options.*' => 'nullable|string|max:255',
            'field_config.remaining_life_options' => 'nullable|array',
            'field_config.remaining_life_options.*' => 'nullable|string|max:255',
            'field_config.report_template' => 'nullable|string',
            'field_config.ai_prompt_helper' => 'nullable|string',
            'levels' => 'nullable|array',
            'levels.*' => 'exists:survey_levels,id',
        ]);

        // Handle icon file upload
        if ($request->hasFile('icon_file')) {
            $iconPath = $request->file('icon_file')->store('icons/sections', 'public');
            // Store path relative to public/storage (which is symlinked to storage/app/public)
            $validated['icon'] = 'storage/' . $iconPath;
        } else {
            // Use custom icon if provided, otherwise use selected icon (FontAwesome class)
            $validated['icon'] = $validated['custom_icon'] ?? ($validated['icon'] ?? null);
        }

        // Handle field_config separately to ensure proper JSON encoding
        if (isset($validated['field_config']) && is_array($validated['field_config'])) {
            // Handle defects_options array - filter out empty values and reindex
            if (isset($validated['field_config']['defects_options'])) {
                $defectsOptions = array_values(array_filter($validated['field_config']['defects_options'], function($value) {
                    return !empty(trim($value));
                }));
                $validated['field_config']['defects_options'] = $defectsOptions ?: ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs'];
            }
            
            // Handle remaining_life_options array - filter out empty values and reindex
            if (isset($validated['field_config']['remaining_life_options'])) {
                $remainingLifeOptions = array_values(array_filter($validated['field_config']['remaining_life_options'], function($value) {
                    return !empty(trim($value));
                }));
                $validated['field_config']['remaining_life_options'] = $remainingLifeOptions ?: ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs'];
            }
        }

        // Extract levels before creating section
        $levels = $validated['levels'] ?? [];
        unset($validated['levels']);

        $section = SurveySection::create($validated);

        // Attach levels if provided
        if (!empty($levels)) {
            $levelsWithOrder = [];
            foreach ($levels as $index => $levelId) {
                $levelsWithOrder[$levelId] = ['sort_order' => $index + 1];
            }
            $section->levels()->sync($levelsWithOrder);
        }

        return redirect()->route('admin.survey-sections.index')
            ->with('success', 'Survey section created successfully.');
    }

    /**
     * Display the specified survey section.
     */
    public function show(SurveySection $surveySection)
    {
        $surveySection->load(['category', 'assessments.survey']);
        
        return view('admin.survey-sections.show', compact('surveySection'));
    }

    /**
     * Show the form for editing the specified survey section.
     */
    public function edit(SurveySection $surveySection)
    {
        $categories = SurveyCategory::active()->ordered()->get();
        $levels = \App\Models\SurveyLevel::active()->ordered()->get();
        $surveySection->load(['fields','levels']);
        
        return view('admin.survey-sections.edit', compact('surveySection', 'categories', 'levels'));
    }

    /**
     * Update the specified survey section.
     */
    public function update(Request $request, SurveySection $surveySection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_sections,name,' . $surveySection->id,
            'display_name' => 'required|string|max:255',
            'survey_category_id' => 'required|exists:survey_categories,id',
            'icon' => 'nullable|string|max:255',
            'custom_icon' => 'nullable|string|max:255',
            'icon_file' => 'nullable|image|max:2048', // 2MB max
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'generation_method' => 'nullable|in:database,ai',
            'field_config' => 'nullable|array',
            'field_config.ai_prompt_template' => 'nullable|string',
            'field_config.defects_options' => 'nullable|array',
            'field_config.defects_options.*' => 'nullable|string|max:255',
            'field_config.remaining_life_options' => 'nullable|array',
            'field_config.remaining_life_options.*' => 'nullable|string|max:255',
            'field_config.report_template' => 'nullable|string',
            'field_config.ai_prompt_helper' => 'nullable|string',
            'levels' => 'nullable|array',
            'levels.*' => 'exists:survey_levels,id',
        ]);

        // Handle icon file upload
        if ($request->hasFile('icon_file')) {
            // Delete old icon file if it exists
            if ($surveySection->icon && strpos($surveySection->icon, 'storage/') !== false) {
                $oldIconPath = str_replace('storage/', '', $surveySection->icon);
                if (\Storage::disk('public')->exists($oldIconPath)) {
                    \Storage::disk('public')->delete($oldIconPath);
                }
            }
            
            $iconPath = $request->file('icon_file')->store('icons/sections', 'public');
            // Store path relative to public/storage (which is symlinked to storage/app/public)
            $validated['icon'] = 'storage/' . $iconPath;
        } else {
            // If no file upload, preserve existing icon unless explicitly changed
            // Use custom icon if provided, otherwise keep existing or use selected icon
            if (!isset($validated['icon'])) {
                // If icon field is not in request, keep existing
                unset($validated['icon']);
            } else {
                $validated['icon'] = $validated['custom_icon'] ?? ($validated['icon'] ?? $surveySection->icon);
            }
        }

        // Handle field_config separately to ensure proper JSON encoding
        if (isset($validated['field_config']) && is_array($validated['field_config'])) {
            // Merge with existing field_config to preserve other settings
            $existingConfig = $surveySection->field_config ?? [];
            
            // Handle defects_options array - filter out empty values and reindex
            if (isset($validated['field_config']['defects_options'])) {
                $defectsOptions = array_values(array_filter($validated['field_config']['defects_options'], function($value) {
                    return !empty(trim($value));
                }));
                $validated['field_config']['defects_options'] = $defectsOptions ?: ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs'];
            }
            
            // Handle remaining_life_options array - filter out empty values and reindex
            if (isset($validated['field_config']['remaining_life_options'])) {
                $remainingLifeOptions = array_values(array_filter($validated['field_config']['remaining_life_options'], function($value) {
                    return !empty(trim($value));
                }));
                $validated['field_config']['remaining_life_options'] = $remainingLifeOptions ?: ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs'];
            }
            
            // Preserve report_template and ai_prompt_helper if they exist
            if (isset($existingConfig['report_template'])) {
                $validated['field_config']['report_template'] = $validated['field_config']['report_template'] ?? $existingConfig['report_template'];
            }
            if (isset($existingConfig['ai_prompt_helper'])) {
                $validated['field_config']['ai_prompt_helper'] = $validated['field_config']['ai_prompt_helper'] ?? $existingConfig['ai_prompt_helper'];
            }
            
            $validated['field_config'] = array_merge($existingConfig, $validated['field_config']);
        }

        $levels = $validated['levels'] ?? null;
        unset($validated['levels']);

        $surveySection->update($validated);

        // Sync levels if provided (or detach when none sent)
        if (is_array($levels)) {
            $levelsWithOrder = [];
            foreach ($levels as $index => $levelId) {
                $levelsWithOrder[$levelId] = ['sort_order' => $index + 1];
            }
            $surveySection->levels()->sync($levelsWithOrder);
        }

        return redirect()->route('admin.survey-sections.index')
            ->with('success', 'Survey section updated successfully.');
    }

    /**
     * Remove the specified survey section.
     */
    public function destroy(SurveySection $surveySection)
    {
        // Check if section has assessments
        if ($surveySection->assessments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete section that has assessments. Please delete assessments first.');
        }

        $surveySection->delete();

        return redirect()->route('admin.survey-sections.index')
            ->with('success', 'Survey section deleted successfully.');
    }

    /**
     * Toggle the active status of a survey section.
     */
    public function toggleStatus(SurveySection $surveySection)
    {
        $surveySection->update(['is_active' => !$surveySection->is_active]);

        $status = $surveySection->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Survey section {$status} successfully.");
    }

    /**
     * Show a field (for AJAX requests).
     */
    public function showField(SurveySection $surveySection, SectionField $field)
    {
        $section = $surveySection; // Alias for clarity
        
        \Log::info('showField called', [
            'section_id' => $section->id,
            'field_id' => $field->id,
            'field_section_id' => $field->survey_section_id,
        ]);
        
        // Ensure field belongs to section
        if ($field->survey_section_id !== $section->id) {
            \Log::warning('Field does not belong to section', [
                'field_section_id' => $field->survey_section_id,
                'requested_section_id' => $section->id,
            ]);
            abort(404);
        }

        // Ensure options are properly formatted
        $fieldData = $field->toArray();
        if (isset($fieldData['options']) && is_string($fieldData['options'])) {
            $decoded = json_decode($fieldData['options'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $fieldData['options'] = $decoded;
            } else {
                $fieldData['options'] = [];
            }
        }
        
        return response()->json($fieldData);
    }

    /**
     * Store a new field for the section.
     */
    public function storeField(Request $request, SurveySection $surveySection)
    {
        $section = $surveySection; // Alias for clarity
        
        // Log incoming request for debugging
        \Log::info('StoreField called', [
            'section_id' => $section->id,
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'request_data' => $request->all(),
        ]);
        
        try {
            $validated = $request->validate([
                'field_label' => 'required|string|max:255',
                'field_type' => 'required|in:textarea,date,numeric,dropdown,single-text,rating',
                'field_order' => 'nullable|integer|min:0',
                'is_required' => 'nullable|boolean',
                'help_text' => 'nullable|string',
                'default_value' => 'nullable|string',
                'options' => 'nullable|string', // JSON array or comma-separated for dropdowns
                'validation_rules' => 'nullable|array',
            ], [
                'field_label.required' => 'Field label is required.',
                'field_type.required' => 'Field type is required.',
            ]);

            // Generate field_key from field_label
            $fieldKey = Str::snake(Str::lower($validated['field_label']));
            
            // Check if field_key already exists for this section
            if (SectionField::where('survey_section_id', $section->id)
                ->where('field_key', $fieldKey)
                ->exists()) {
                $fieldKey .= '_' . time();
            }

            // Process options for dropdown
            $options = null;
            if ($validated['field_type'] === 'dropdown' && !empty($validated['options'])) {
                // Check if it's JSON string or comma-separated
                $decoded = json_decode($validated['options'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $options = array_filter(array_map('trim', $decoded));
                } else {
                    // Fallback to comma-separated
                    $options = array_filter(array_map('trim', explode(',', $validated['options'])));
                }
                
                // Ensure at least one option
                if (empty($options)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'options' => 'At least one option is required for dropdown fields.'
                    ]);
                }
            }

            // Handle validation rules
            $validationRules = $validated['validation_rules'] ?? [];

            $field = SectionField::create([
                'survey_section_id' => $section->id,
                'field_key' => $fieldKey,
                'field_label' => $validated['field_label'],
                'field_type' => $validated['field_type'],
                'field_order' => $validated['field_order'] ?? ((int)$section->fields()->max('field_order') ?? 0) + 1,
                'is_required' => $request->has('is_required') && ($request->input('is_required') == '1' || $request->input('is_required') === true),
                'help_text' => $validated['help_text'] ?? null,
                'default_value' => $validated['default_value'] ?? null,
                'options' => $options,
                'validation_rules' => $validationRules,
                'is_active' => true,
            ]);

            \Log::info('Field created successfully', ['field_id' => $field->id, 'section_id' => $section->id]);

            // Redirect back with success message
            return redirect()->route('admin.survey-sections.edit', $section)
                ->with('success', 'Field added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating field', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Error creating field: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing field.
     */
    public function updateField(Request $request, SurveySection $surveySection, SectionField $field)
    {
        $section = $surveySection; // Alias for clarity
        // Ensure field belongs to section
        if ($field->survey_section_id !== $section->id) {
            abort(404);
        }

        $validated = $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:textarea,date,numeric,dropdown,single-text,rating',
            'field_order' => 'nullable|integer|min:0',
            'is_required' => 'boolean',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'options' => 'nullable|string',
            'validation_rules' => 'nullable|array',
        ]);

        // Process options for dropdown
        $options = null;
        if ($validated['field_type'] === 'dropdown' && !empty($validated['options'])) {
            // Check if it's JSON string or comma-separated
            $decoded = json_decode($validated['options'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $options = array_filter(array_map('trim', $decoded));
            } else {
                // Fallback to comma-separated
                $options = array_filter(array_map('trim', explode(',', $validated['options'])));
            }
            
            // Ensure at least one option
            if (empty($options)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options' => 'At least one option is required for dropdown fields.'
                ]);
            }
        }

        $field->update([
            'field_label' => $validated['field_label'],
            'field_type' => $validated['field_type'],
            'field_order' => $validated['field_order'] ?? $field->field_order,
            'is_required' => $request->has('is_required') && ($request->input('is_required') == '1' || $request->input('is_required') === true),
            'help_text' => $validated['help_text'] ?? null,
            'default_value' => $validated['default_value'] ?? null,
            'options' => $options,
            'validation_rules' => $validated['validation_rules'] ?? [],
        ]);

        return redirect()->route('admin.survey-sections.edit', $section)
            ->with('success', 'Field updated successfully.');
    }

    /**
     * Delete a field.
     */
    public function deleteField(SurveySection $surveySection, SectionField $field)
    {
        $section = $surveySection; // Alias for clarity
        // Ensure field belongs to section
        if ($field->survey_section_id !== $section->id) {
            abort(404);
        }

        $field->delete();

        return redirect()->route('admin.survey-sections.edit', $section)
            ->with('success', 'Field deleted successfully.');
    }

    /**
     * Reorder fields.
     */
    public function reorderFields(Request $request, SurveySection $surveySection)
    {
        $section = $surveySection; // Alias for clarity
        $validated = $request->validate([
            'field_orders' => 'required|array',
            'field_orders.*' => 'required|integer',
        ]);

        foreach ($validated['field_orders'] as $fieldId => $order) {
            SectionField::where('id', $fieldId)
                ->where('survey_section_id', $section->id)
                ->update(['field_order' => $order]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fields reordered successfully.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Fields reordered successfully.');
    }
}