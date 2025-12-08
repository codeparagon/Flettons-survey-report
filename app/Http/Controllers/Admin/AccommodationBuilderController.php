<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyAccommodationType;
use App\Models\SurveyAccommodationComponent;
use App\Models\SurveyAccommodationOptionType;
use App\Models\SurveyAccommodationOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AccommodationBuilderController extends Controller
{
    /**
     * Display the accommodation builder.
     */
    public function index()
    {
        $accommodationTypes = SurveyAccommodationType::orderBy('sort_order')->get();
        
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
        
        return view('admin.accommodation-builder.index', compact(
            'accommodationTypes',
            'components',
            'optionTypes',
            'materialsByComponent',
            'globalDefects'
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
        ]);
        
        $validated['key_name'] = Str::slug($validated['key_name'], '_');
        $validated['sort_order'] = $validated['sort_order'] ?? SurveyAccommodationType::max('sort_order') + 1;
        $validated['is_active'] = true;
        
        $type = SurveyAccommodationType::create($validated);
        
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
        ]);
        
        $type->update($validated);
        
        return response()->json([
            'success' => true,
            'type' => $type
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

