<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyAccommodationType;
use App\Models\SurveyAccommodationComponent;
use App\Models\SurveyAccommodationOptionType;
use App\Models\SurveyAccommodationOption;
use App\Models\SurveyLevel;
use App\Models\Survey;
use App\Models\SurveyAccommodationAssessment;
use App\Models\SurveyAccommodationComponentAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccommodationBuilderController extends Controller
{
    /**
     * Display the accommodation builder.
     */
    public function index()
    {
        $accommodationTypes = SurveyAccommodationType::with('components')->orderBy('sort_order')->get();
        
        $components = SurveyAccommodationComponent::orderBy('sort_order')->get();
        
        // Get option types with their options
        $optionTypes = SurveyAccommodationOptionType::with(['options' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->get()
        ->keyBy('key_name');
        
        // Get materials grouped by component
        $materialsByComponent = [];
        $materialType = SurveyAccommodationOptionType::where('key_name', 'material')->first();
        if ($materialType) {
            $materials = SurveyAccommodationOption::where('option_type_id', $materialType->id)
                ->where('scope_type', 'component')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            foreach ($materials as $material) {
                if (!isset($materialsByComponent[$material->scope_id])) {
                    $materialsByComponent[$material->scope_id] = [];
                }
                $materialsByComponent[$material->scope_id][] = $material;
            }
        }
        
        // Get global defects
        $defectType = SurveyAccommodationOptionType::where('key_name', 'defects')->first();
        $globalDefects = [];
        if ($defectType) {
            $globalDefects = SurveyAccommodationOption::where('option_type_id', $defectType->id)
                ->where('scope_type', 'global')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }
        
        // Get survey levels for level selection
        $levels = SurveyLevel::active()->ordered()->get();
        
        return view('admin.accommodation-builder.index', compact(
            'accommodationTypes',
            'components',
            'optionTypes',
            'materialsByComponent',
            'globalDefects',
            'levels'
        ));
    }

    // ===================
    // ACCOMMODATION TYPES API
    // ===================

    /**
     * Store a new accommodation type.
     */
    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'key_name' => 'required|string|max:50',
            'display_name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'levels' => 'nullable|array',
            'levels.*' => 'integer|exists:survey_levels,id',
        ]);
        
        $validated['key_name'] = Str::slug($validated['key_name'], '_');
        $validated['sort_order'] = $validated['sort_order'] ?? SurveyAccommodationType::max('sort_order') + 1;
        $validated['is_active'] = true;
        
        $type = SurveyAccommodationType::create($validated);
        
        // Attach to levels
        if (!empty($validated['levels'])) {
            foreach ($validated['levels'] as $index => $levelId) {
                DB::table('survey_level_accommodation_types')->insert([
                    'survey_level_id' => $levelId,
                    'accommodation_type_id' => $type->id,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'type' => $type,
            'html' => view('admin.accommodation-builder.partials.type-item', ['type' => $type])->render()
        ]);
    }

    /**
     * Update an accommodation type.
     */
    public function updateType(Request $request, SurveyAccommodationType $type)
    {
        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'levels' => 'nullable|array',
            'levels.*' => 'integer|exists:survey_levels,id',
        ]);
        
        $type->update($validated);
        
        // Update level associations
        if ($request->has('levels')) {
            DB::table('survey_level_accommodation_types')
                ->where('accommodation_type_id', $type->id)
                ->delete();
            
            foreach ($request->input('levels', []) as $index => $levelId) {
                DB::table('survey_level_accommodation_types')->insert([
                    'survey_level_id' => $levelId,
                    'accommodation_type_id' => $type->id,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'type' => $type
        ]);
    }

    /**
     * Get an accommodation type with assigned levels.
     */
    public function getType(SurveyAccommodationType $type)
    {
        $levelIds = DB::table('survey_level_accommodation_types')
            ->where('accommodation_type_id', $type->id)
            ->pluck('survey_level_id')
            ->toArray();
        
        return response()->json([
            'success' => true,
            'type' => $type,
            'levels' => $levelIds
        ]);
    }

    /**
     * Delete an accommodation type.
     */
    public function deleteType(SurveyAccommodationType $type)
    {
        // Check if type has assessments
        if ($type->assessments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete accommodation type with existing assessments.'
            ], 400);
        }
        
        // Delete level associations
        DB::table('survey_level_accommodation_types')
            ->where('accommodation_type_id', $type->id)
            ->delete();
        
        $type->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Clone an accommodation type.
     */
    public function cloneType(SurveyAccommodationType $type)
    {
        $newType = $type->replicate();
        $newType->key_name = $type->key_name . '_copy_' . time();
        $newType->display_name = $type->display_name . ' (Copy)';
        $newType->sort_order = SurveyAccommodationType::max('sort_order') + 1;
        $newType->save();
        
        // Copy level associations
        $levelAssociations = DB::table('survey_level_accommodation_types')
            ->where('accommodation_type_id', $type->id)
            ->get();
        
        foreach ($levelAssociations as $assoc) {
            DB::table('survey_level_accommodation_types')->insert([
                'survey_level_id' => $assoc->survey_level_id,
                'accommodation_type_id' => $newType->id,
                'sort_order' => $assoc->sort_order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'type' => $newType,
            'html' => view('admin.accommodation-builder.partials.type-item', ['type' => $newType])->render()
        ]);
    }

    /**
     * Reorder accommodation types.
     */
    public function reorderTypes(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:survey_accommodation_types,id',
        ]);
        
        foreach ($validated['order'] as $index => $typeId) {
            SurveyAccommodationType::where('id', $typeId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Get components for a specific accommodation type.
     */
    public function getTypeComponents(SurveyAccommodationType $type)
    {
        $type->load('components');
        
        $allComponents = SurveyAccommodationComponent::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $assignedComponents = $type->components->keyBy('id');
        
        $components = $allComponents->map(function($component) use ($assignedComponents) {
            $assigned = $assignedComponents->get($component->id);
            return [
                'id' => $component->id,
                'key_name' => $component->key_name,
                'display_name' => $component->display_name,
                'is_assigned' => $assigned !== null,
                'is_required' => $assigned ? (bool) $assigned->pivot->is_required : false,
                'sort_order' => $assigned ? $assigned->pivot->sort_order : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'components' => $components,
        ]);
    }

    /**
     * Update components for a specific accommodation type.
     */
    public function updateTypeComponents(Request $request, SurveyAccommodationType $type)
    {
        $validated = $request->validate([
            'components' => 'required|array',
            'components.*.component_id' => 'required|integer|exists:survey_accommodation_components,id',
            'components.*.is_required' => 'nullable|boolean',
            'components.*.sort_order' => 'nullable|integer|min:0',
        ]);
        
        // Prepare sync data
        $syncData = [];
        foreach ($validated['components'] as $index => $componentData) {
            $componentId = $componentData['component_id'];
            $syncData[$componentId] = [
                'is_required' => $componentData['is_required'] ?? false,
                'sort_order' => $componentData['sort_order'] ?? $index,
            ];
        }
        
        // Sync components with pivot data
        $type->components()->sync($syncData);
        
        // Reload to get updated data
        $type->load('components');
        
        return response()->json([
            'success' => true,
            'message' => 'Components updated successfully',
            'type' => $type,
            'components' => $type->components->map(function($component) {
                return [
                    'id' => $component->id,
                    'key_name' => $component->key_name,
                    'display_name' => $component->display_name,
                    'is_required' => (bool) $component->pivot->is_required,
                    'sort_order' => $component->pivot->sort_order,
                ];
            }),
        ]);
    }

    // ===================
    // ACCOMMODATION COMPONENTS API
    // ===================

    /**
     * Store a new accommodation component.
     */
    public function storeComponent(Request $request)
    {
        $validated = $request->validate([
            'key_name' => 'required|string|max:50',
            'display_name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $validated['key_name'] = Str::slug($validated['key_name'], '_');
        $validated['sort_order'] = $validated['sort_order'] ?? SurveyAccommodationComponent::max('sort_order') + 1;
        $validated['is_active'] = true;
        
        $component = SurveyAccommodationComponent::create($validated);
        
        return response()->json([
            'success' => true,
            'component' => $component,
            'html' => view('admin.accommodation-builder.partials.component-item', ['component' => $component, 'materials' => []])->render()
        ]);
    }

    /**
     * Update an accommodation component.
     */
    public function updateComponent(Request $request, SurveyAccommodationComponent $component)
    {
        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $component->update($validated);
        
        return response()->json([
            'success' => true,
            'component' => $component
        ]);
    }

    /**
     * Delete an accommodation component.
     */
    public function deleteComponent(SurveyAccommodationComponent $component)
    {
        // Check if component has assessments
        if ($component->componentAssessments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete component with existing assessments.'
            ], 400);
        }
        
        // Delete associated options
        $materialType = SurveyAccommodationOptionType::where('key_name', 'material')->first();
        if ($materialType) {
            SurveyAccommodationOption::where('option_type_id', $materialType->id)
                ->where('scope_type', 'component')
                ->where('scope_id', $component->id)
                ->delete();
        }
        
        $component->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Reorder accommodation components.
     */
    public function reorderComponents(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:survey_accommodation_components,id',
        ]);
        
        foreach ($validated['order'] as $index => $componentId) {
            SurveyAccommodationComponent::where('id', $componentId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    // ===================
    // ACCOMMODATION OPTIONS API
    // ===================

    /**
     * Store a new accommodation option (material or defect).
     */
    public function storeOption(Request $request)
    {
        $validated = $request->validate([
            'option_type' => 'required|in:material,defects',
            'value' => 'required|string|max:255',
            'component_id' => 'nullable|exists:survey_accommodation_components,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $optionType = SurveyAccommodationOptionType::where('key_name', $validated['option_type'])->first();
        if (!$optionType) {
            // Create option type if it doesn't exist
            $optionType = SurveyAccommodationOptionType::create([
                'key_name' => $validated['option_type'],
                'label' => ucfirst($validated['option_type']),
                'is_multiple' => $validated['option_type'] === 'defects',
                'sort_order' => 0,
                'is_active' => true,
            ]);
        }
        
        $scopeType = 'global';
        $scopeId = null;
        
        if ($validated['option_type'] === 'material' && !empty($validated['component_id'])) {
            $scopeType = 'component';
            $scopeId = $validated['component_id'];
        }
        
        // Calculate sort order
        $sortOrder = $validated['sort_order'] ?? SurveyAccommodationOption::where('option_type_id', $optionType->id)
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->max('sort_order') + 1;
        
        $option = SurveyAccommodationOption::create([
            'option_type_id' => $optionType->id,
            'value' => $validated['value'],
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'option' => $option
        ]);
    }

    /**
     * Update an accommodation option.
     */
    public function updateOption(Request $request, SurveyAccommodationOption $accommodationOption)
    {
        $validated = $request->validate([
            'value' => 'sometimes|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $accommodationOption->update($validated);
        
        return response()->json([
            'success' => true,
            'option' => $accommodationOption
        ]);
    }

    /**
     * Delete an accommodation option.
     */
    public function deleteOption(SurveyAccommodationOption $accommodationOption)
    {
        $accommodationOption->delete();
        
        return response()->json(['success' => true]);
    }

}

