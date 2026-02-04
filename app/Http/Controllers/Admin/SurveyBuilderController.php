<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use App\Models\SurveySectionDefinition;
use App\Models\SurveyLevel;
use App\Models\SurveyOptionType;
use App\Models\SurveyOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SurveyBuilderController extends Controller
{
    /**
     * Display the survey builder.
     */
    public function index()
    {
        $categories = SurveyCategory::ordered()
            ->with(['subcategories' => function($query) {
                $query->ordered()->with(['sectionDefinitions' => function($q) {
                    $q->ordered();
                }]);
            }])
            ->get();
        
        $levels = SurveyLevel::active()->ordered()->get();
        
        return view('admin.survey-builder.index', compact('categories', 'levels'));
    }

    // ===================
    // CATEGORIES API
    // ===================

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $validated['name'] = Str::slug($validated['name'], '_');
        $validated['sort_order'] = $validated['sort_order'] ?? SurveyCategory::max('sort_order') + 1;
        $validated['is_active'] = true;
        
        $category = SurveyCategory::create($validated);
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'html' => view('admin.survey-builder.partials.category-item', ['category' => $category])->render()
        ]);
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, SurveyCategory $category)
    {
        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:100',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $category->update($validated);
        
        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(SurveyCategory $category)
    {
        // Delete all subcategories and their sections
        foreach ($category->subcategories as $subcategory) {
            // Delete section level associations
            foreach ($subcategory->sectionDefinitions as $section) {
                DB::table('survey_level_section_definitions')
                    ->where('section_definition_id', $section->id)
                    ->delete();
            }
            $subcategory->sectionDefinitions()->delete();
        }
        $category->subcategories()->delete();
        $category->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Reorder categories.
     */
    public function reorderCategories(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:survey_categories,id',
        ]);
        
        foreach ($validated['order'] as $index => $categoryId) {
            SurveyCategory::where('id', $categoryId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    // ===================
    // SUBCATEGORIES API
    // ===================

    /**
     * Store a new subcategory.
     */
    public function storeSubcategory(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:survey_categories,id',
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $validated['name'] = Str::slug($validated['name'], '_');
        $validated['sort_order'] = $validated['sort_order'] ?? SurveySubcategory::where('category_id', $validated['category_id'])->max('sort_order') + 1;
        $validated['is_active'] = true;
        
        $subcategory = SurveySubcategory::create($validated);
        $subcategory->load('category');
        
        return response()->json([
            'success' => true,
            'subcategory' => $subcategory,
            'html' => view('admin.survey-builder.partials.subcategory-item', ['subcategory' => $subcategory])->render()
        ]);
    }

    /**
     * Update a subcategory.
     */
    public function updateSubcategory(Request $request, SurveySubcategory $subcategory)
    {
        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $subcategory->update($validated);
        
        return response()->json([
            'success' => true,
            'subcategory' => $subcategory
        ]);
    }

    /**
     * Delete a subcategory.
     */
    public function deleteSubcategory(SurveySubcategory $subcategory)
    {
        // Delete section level associations
        foreach ($subcategory->sectionDefinitions as $section) {
            DB::table('survey_level_section_definitions')
                ->where('section_definition_id', $section->id)
                ->delete();
        }
        $subcategory->sectionDefinitions()->delete();
        $subcategory->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Reorder subcategories.
     */
    public function reorderSubcategories(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:survey_subcategories,id',
        ]);
        
        foreach ($validated['order'] as $index => $subcategoryId) {
            SurveySubcategory::where('id', $subcategoryId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    // ===================
    // SECTION DEFINITIONS API
    // ===================

    /**
     * Get a section definition for editing.
     */
    public function getSection(SurveySectionDefinition $section)
    {
        // Get the level IDs this section belongs to
        $levelIds = DB::table('survey_level_section_definitions')
            ->where('section_definition_id', $section->id)
            ->pluck('survey_level_id')
            ->toArray();
        
        return response()->json([
            'success' => true,
            'section' => $section,
            'levels' => $levelIds
        ]);
    }

    /**
     * Store a new section definition.
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'subcategory_id' => 'required|exists:survey_subcategories,id',
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:100',
            'is_clonable' => 'nullable|boolean',
            'max_clones' => 'nullable|integer|min:1|max:20',
            'levels' => 'nullable|array',
            'levels.*' => 'integer|exists:survey_levels,id',
        ]);
        
        $validated['name'] = Str::slug($validated['name'], '_');
        $validated['sort_order'] = SurveySectionDefinition::where('subcategory_id', $validated['subcategory_id'])->max('sort_order') + 1;
        $validated['is_active'] = true;
        $validated['is_clonable'] = $request->input('is_clonable', false) ? true : false;
        
        $section = SurveySectionDefinition::create($validated);
        
        // Attach to levels
        if (!empty($validated['levels'])) {
            foreach ($validated['levels'] as $index => $levelId) {
                DB::table('survey_level_section_definitions')->insert([
                    'survey_level_id' => $levelId,
                    'section_definition_id' => $section->id,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $section->load('subcategory.category');
        
        return response()->json([
            'success' => true,
            'section' => $section,
            'html' => view('admin.survey-builder.partials.section-item', ['section' => $section])->render()
        ]);
    }

    /**
     * Update a section definition.
     */
    public function updateSection(Request $request, SurveySectionDefinition $section)
    {
        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:100',
            'name' => 'sometimes|string|max:100',
            'is_clonable' => 'nullable|boolean',
            'max_clones' => 'nullable|integer|min:1|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'levels' => 'nullable|array',
            'levels.*' => 'integer|exists:survey_levels,id',
        ]);
        
        if (isset($validated['name'])) {
            $validated['name'] = Str::slug($validated['name'], '_');
        }
        
        $validated['is_clonable'] = $request->input('is_clonable', false) ? true : false;
        
        $section->update($validated);
        
        // Update level associations
        if ($request->has('levels')) {
            DB::table('survey_level_section_definitions')
                ->where('section_definition_id', $section->id)
                ->delete();
            
            foreach ($request->input('levels', []) as $index => $levelId) {
                DB::table('survey_level_section_definitions')->insert([
                    'survey_level_id' => $levelId,
                    'section_definition_id' => $section->id,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'section' => $section
        ]);
    }

    /**
     * Delete a section definition.
     */
    public function deleteSection(SurveySectionDefinition $section)
    {
        // Check if section has assessments
        if ($section->assessments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete section with existing assessments.'
            ], 400);
        }
        
        // Delete level associations
        DB::table('survey_level_section_definitions')
            ->where('section_definition_id', $section->id)
            ->delete();
        
        // Delete required fields associations
        DB::table('survey_section_required_fields')
            ->where('section_definition_id', $section->id)
            ->delete();
        
        $section->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Clone a section definition.
     */
    public function cloneSection(SurveySectionDefinition $section)
    {
        $newSection = $section->replicate();
        $newSection->name = $section->name . '_copy_' . time();
        $newSection->display_name = $section->display_name . ' (Copy)';
        $newSection->sort_order = SurveySectionDefinition::where('subcategory_id', $section->subcategory_id)->max('sort_order') + 1;
        $newSection->save();
        
        // Copy level associations
        $levelAssociations = DB::table('survey_level_section_definitions')
            ->where('section_definition_id', $section->id)
            ->get();
        
        foreach ($levelAssociations as $assoc) {
            DB::table('survey_level_section_definitions')->insert([
                'survey_level_id' => $assoc->survey_level_id,
                'section_definition_id' => $newSection->id,
                'sort_order' => $assoc->sort_order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Copy required fields
        $requiredFields = DB::table('survey_section_required_fields')
            ->where('section_definition_id', $section->id)
            ->get();
        
        foreach ($requiredFields as $field) {
            DB::table('survey_section_required_fields')->insert([
                'section_definition_id' => $newSection->id,
                'option_type_id' => $field->option_type_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $newSection->load('subcategory.category');
        
        return response()->json([
            'success' => true,
            'section' => $newSection,
            'html' => view('admin.survey-builder.partials.section-item', ['section' => $newSection])->render()
        ]);
    }

    /**
     * Reorder section definitions.
     */
    public function reorderSections(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:survey_section_definitions,id',
        ]);
        
        foreach ($validated['order'] as $index => $sectionId) {
            SurveySectionDefinition::where('id', $sectionId)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    // ===================
    // BULK ACTIONS API
    // ===================

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:enable,disable,delete',
            'type' => 'required|in:category,subcategory,section',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        
        $action = $validated['action'];
        $type = $validated['type'];
        $ids = $validated['ids'];
        
        switch ($type) {
            case 'category':
                $model = SurveyCategory::class;
                break;
            case 'subcategory':
                $model = SurveySubcategory::class;
                break;
            case 'section':
                $model = SurveySectionDefinition::class;
                break;
        }
        
        switch ($action) {
            case 'enable':
                $model::whereIn('id', $ids)->update(['is_active' => true]);
                break;
            case 'disable':
                $model::whereIn('id', $ids)->update(['is_active' => false]);
                break;
            case 'delete':
                if ($type === 'section') {
                    // Delete level associations first
                    DB::table('survey_level_section_definitions')
                        ->whereIn('section_definition_id', $ids)
                        ->delete();
                    DB::table('survey_section_required_fields')
                        ->whereIn('section_definition_id', $ids)
                        ->delete();
                }
                $model::whereIn('id', $ids)->delete();
                break;
        }
        
        return response()->json(['success' => true]);
    }

    // ===================
    // PREVIEW API
    // ===================

    /**
     * Generate preview content for a section.
     */
    public function preview(Request $request)
    {
        $sectionId = $request->input('section_id');
        
        if (!$sectionId) {
            return response()->json([
                'html' => '<div class="preview-empty"><i class="fas fa-hand-pointer"></i><p>Click on a section to preview.</p></div>'
            ]);
        }
        
        $section = SurveySectionDefinition::with(['subcategory.category', 'requiredFields'])
            ->find($sectionId);
        
        if (!$section) {
            return response()->json([
                'html' => '<div class="preview-empty"><i class="fas fa-exclamation-triangle"></i><p>Section not found.</p></div>'
            ]);
        }
        
        // Load option types with their options
        $optionTypes = SurveyOptionType::with(['options' => function($query) use ($section) {
            $query->where('is_active', true)
                ->where(function($q) use ($section) {
                    // Global options
                    $q->where('scope_type', 'global');
                    
                    // Category-scoped options (if subcategory exists)
                    if ($section->subcategory && $section->subcategory->category_id) {
                        $q->orWhere(function($q2) use ($section) {
                            $q2->where('scope_type', 'category')
                               ->where('scope_id', $section->subcategory->category_id);
                        });
                    }
                    
                    // Subcategory-scoped options (if subcategory exists)
                    if ($section->subcategory_id) {
                        $q->orWhere(function($q3) use ($section) {
                            $q3->where('scope_type', 'subcategory')
                               ->where('scope_id', $section->subcategory_id);
                        });
                    }
                })
                ->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get()
        ->keyBy('key_name');
        
        $html = view('admin.survey-builder.partials.preview-content', compact('section', 'optionTypes'))->render();
        
        return response()->json(['html' => $html]);
    }
}

