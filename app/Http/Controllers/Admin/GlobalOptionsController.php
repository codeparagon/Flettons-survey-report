<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyOptionType;
use App\Models\SurveyOption;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GlobalOptionsController extends Controller
{
    /**
     * Display the global options manager.
     */
    public function index()
    {
        // Get all option types with their options grouped by scope
        $optionTypes = SurveyOptionType::with(['options' => function($query) {
            $query->orderBy('scope_type')->orderBy('scope_id')->orderBy('sort_order');
        }])
        ->orderBy('sort_order')
        ->get();
        
        // Organize options by type and scope
        $organizedOptions = [];
        foreach ($optionTypes as $type) {
            $organizedOptions[$type->key_name] = [
                'type' => $type,
                'global' => $type->options->where('scope_type', 'global'),
                'by_category' => [],
                'by_subcategory' => [],
            ];
            
            // Group category-scoped options
            $categoryOptions = $type->options->where('scope_type', 'category');
            foreach ($categoryOptions as $option) {
                $category = SurveyCategory::find($option->scope_id);
                if ($category) {
                    if (!isset($organizedOptions[$type->key_name]['by_category'][$category->id])) {
                        $organizedOptions[$type->key_name]['by_category'][$category->id] = [
                            'category' => $category,
                            'options' => collect()
                        ];
                    }
                    $organizedOptions[$type->key_name]['by_category'][$category->id]['options']->push($option);
                }
            }
            
            // Group subcategory-scoped options
            $subcategoryOptions = $type->options->where('scope_type', 'subcategory');
            foreach ($subcategoryOptions as $option) {
                $subcategory = SurveySubcategory::with('category')->find($option->scope_id);
                if ($subcategory) {
                    if (!isset($organizedOptions[$type->key_name]['by_subcategory'][$subcategory->id])) {
                        $organizedOptions[$type->key_name]['by_subcategory'][$subcategory->id] = [
                            'subcategory' => $subcategory,
                            'options' => collect()
                        ];
                    }
                    $organizedOptions[$type->key_name]['by_subcategory'][$subcategory->id]['options']->push($option);
                }
            }
        }
        
        // Get categories for scope selection
        $categories = SurveyCategory::with('subcategories')->orderBy('sort_order')->get();
        
        return view('admin.survey-options.index', compact('optionTypes', 'organizedOptions', 'categories'));
    }

    // ===================
    // OPTION TYPES API
    // ===================

    /**
     * Store a new option type.
     */
    public function storeOptionType(Request $request)
    {
        $validated = $request->validate([
            'key_name' => 'required|string|max:50|unique:survey_option_types,key_name',
            'label' => 'required|string|max:100',
            'is_multiple' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $validated['key_name'] = Str::slug($validated['key_name'], '_');
        $validated['sort_order'] = $validated['sort_order'] ?? SurveyOptionType::max('sort_order') + 1;
        $validated['is_active'] = true;
        $validated['is_multiple'] = $validated['is_multiple'] ?? false;
        
        $optionType = SurveyOptionType::create($validated);
        
        return response()->json([
            'success' => true,
            'option_type' => $optionType
        ]);
    }

    /**
     * Update an option type.
     */
    public function updateOptionType(Request $request, SurveyOptionType $optionType)
    {
        $validated = $request->validate([
            'label' => 'sometimes|string|max:100',
            'is_multiple' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $optionType->update($validated);
        
        return response()->json([
            'success' => true,
            'option_type' => $optionType
        ]);
    }

    /**
     * Delete an option type.
     */
    public function deleteOptionType(SurveyOptionType $optionType)
    {
        // Check if option type has options
        if ($optionType->options()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete option type with existing options. Delete options first.'
            ], 400);
        }
        
        $optionType->delete();
        
        return response()->json(['success' => true]);
    }

    // ===================
    // OPTIONS API
    // ===================

    /**
     * Store a new option.
     */
    public function storeOption(Request $request)
    {
        $validated = $request->validate([
            'option_type_id' => 'required|exists:survey_option_types,id',
            'value' => 'required|string|max:255',
            'scope_type' => 'required|in:global,category,subcategory',
            'scope_id' => 'nullable|integer',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        // Validate scope_id based on scope_type
        if ($validated['scope_type'] === 'category' && !empty($validated['scope_id'])) {
            if (!SurveyCategory::find($validated['scope_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ], 400);
            }
        } elseif ($validated['scope_type'] === 'subcategory' && !empty($validated['scope_id'])) {
            if (!SurveySubcategory::find($validated['scope_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid subcategory ID'
                ], 400);
            }
        } elseif ($validated['scope_type'] === 'global') {
            $validated['scope_id'] = null;
        }
        
        // Calculate sort order
        $sortOrder = $validated['sort_order'] ?? SurveyOption::where('option_type_id', $validated['option_type_id'])
            ->where('scope_type', $validated['scope_type'])
            ->where('scope_id', $validated['scope_id'])
            ->max('sort_order') + 1;
        
        $option = SurveyOption::create([
            'option_type_id' => $validated['option_type_id'],
            'value' => $validated['value'],
            'scope_type' => $validated['scope_type'],
            'scope_id' => $validated['scope_id'],
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'option' => $option
        ]);
    }

    /**
     * Update an option.
     */
    public function updateOption(Request $request, SurveyOption $option)
    {
        $validated = $request->validate([
            'value' => 'sometimes|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $option->update($validated);
        
        return response()->json([
            'success' => true,
            'option' => $option
        ]);
    }

    /**
     * Delete an option.
     */
    public function deleteOption(SurveyOption $option)
    {
        $option->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Reorder options.
     */
    public function reorderOptions(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:survey_options,id',
        ]);
        
        foreach ($validated['order'] as $index => $optionId) {
            SurveyOption::where('id', $optionId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete options.
     */
    public function bulkDeleteOptions(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:survey_options,id',
        ]);
        
        SurveyOption::whereIn('id', $validated['ids'])->delete();
        
        return response()->json(['success' => true]);
    }
}













