<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyContentSection;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use App\Models\SurveyLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentSectionController extends Controller
{
    /**
     * Display a listing of content sections.
     */
    public function index()
    {
        $sections = SurveyContentSection::with(['category', 'subcategory'])
            ->ordered()
            ->paginate(15);
        
        return view('admin.content-sections.index', compact('sections'));
    }

    /**
     * Show the wizard form for creating a new content section.
     */
    public function create()
    {
        $categories = SurveyCategory::active()->ordered()->get();
        $subcategories = SurveySubcategory::active()->ordered()->with('category')->get();
        $levels = SurveyLevel::active()->ordered()->get();
        
        return view('admin.content-sections.wizard', [
            'section' => null,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'levels' => $levels,
            'selectedLevels' => [],
            'linkType' => 'standalone',
            'tagsString' => '',
        ]);
    }

    /**
     * Store a newly created content section.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'link_type' => 'required|in:standalone,category,subcategory',
            'category_id' => 'nullable|required_if:link_type,category,subcategory|exists:survey_categories,id',
            'subcategory_id' => 'nullable|required_if:link_type,subcategory|exists:survey_subcategories,id',
            'tags' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'levels' => 'nullable|array',
            'levels.*' => 'integer|exists:survey_levels,id',
        ]);

        // Process tags (convert comma-separated string to array)
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        } else {
            $validated['tags'] = null;
        }

        // Set category/subcategory based on link_type
        if ($validated['link_type'] === 'standalone') {
            $validated['category_id'] = null;
            $validated['subcategory_id'] = null;
        } elseif ($validated['link_type'] === 'category') {
            $validated['subcategory_id'] = null;
        }

        // Set default sort_order if not provided
        if (!isset($validated['sort_order']) || $validated['sort_order'] === '') {
            $maxOrder = SurveyContentSection::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        // Set is_active default
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Remove link_type from validated data (not a database field)
        unset($validated['link_type']);
        
        // Extract levels before creating section
        $levels = $validated['levels'] ?? [];
        unset($validated['levels']);

        $section = SurveyContentSection::create($validated);
        
        // Attach to levels
        if (!empty($levels)) {
            foreach ($levels as $index => $levelId) {
                DB::table('survey_level_content_sections')->insert([
                    'survey_level_id' => $levelId,
                    'content_section_id' => $section->id,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.content-sections.index')
            ->with('success', 'Content section created successfully.');
    }

    /**
     * Display the specified content section.
     */
    public function show(SurveyContentSection $contentSection)
    {
        $contentSection->load(['category', 'subcategory', 'levels']);
        
        return view('admin.content-sections.show', compact('contentSection'));
    }

    /**
     * Show the wizard form for editing the specified content section.
     */
    public function edit(SurveyContentSection $contentSection)
    {
        $categories = SurveyCategory::active()->ordered()->get();
        $subcategories = SurveySubcategory::active()->ordered()->with('category')->get();
        $levels = SurveyLevel::active()->ordered()->get();
        
        // Get assigned level IDs
        $selectedLevels = DB::table('survey_level_content_sections')
            ->where('content_section_id', $contentSection->id)
            ->pluck('survey_level_id')
            ->toArray();
        
        // Determine link_type
        $linkType = 'standalone';
        if ($contentSection->subcategory_id) {
            $linkType = 'subcategory';
        } elseif ($contentSection->category_id) {
            $linkType = 'category';
        }

        // Convert tags array to comma-separated string
        $tagsString = $contentSection->tags ? implode(', ', $contentSection->tags) : '';

        return view('admin.content-sections.wizard', [
            'section' => $contentSection,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'levels' => $levels,
            'selectedLevels' => $selectedLevels,
            'linkType' => $linkType,
            'tagsString' => $tagsString,
        ]);
    }

    /**
     * Update the specified content section.
     */
    public function update(Request $request, SurveyContentSection $contentSection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'link_type' => 'required|in:standalone,category,subcategory',
            'category_id' => 'nullable|required_if:link_type,category,subcategory|exists:survey_categories,id',
            'subcategory_id' => 'nullable|required_if:link_type,subcategory|exists:survey_subcategories,id',
            'tags' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'levels' => 'nullable|array',
            'levels.*' => 'integer|exists:survey_levels,id',
        ]);

        // Process tags (convert comma-separated string to array)
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        } else {
            $validated['tags'] = null;
        }

        // Set category/subcategory based on link_type
        if ($validated['link_type'] === 'standalone') {
            $validated['category_id'] = null;
            $validated['subcategory_id'] = null;
        } elseif ($validated['link_type'] === 'category') {
            $validated['subcategory_id'] = null;
        }

        // Set is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Remove link_type from validated data (not a database field)
        unset($validated['link_type']);
        
        // Extract levels before updating section
        $levels = $validated['levels'] ?? [];
        unset($validated['levels']);

        $contentSection->update($validated);
        
        // Update level associations
        DB::table('survey_level_content_sections')
            ->where('content_section_id', $contentSection->id)
            ->delete();
        
        if (!empty($levels)) {
            foreach ($levels as $index => $levelId) {
                DB::table('survey_level_content_sections')->insert([
                    'survey_level_id' => $levelId,
                    'content_section_id' => $contentSection->id,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.content-sections.index')
            ->with('success', 'Content section updated successfully.');
    }

    /**
     * Remove the specified content section.
     */
    public function destroy(SurveyContentSection $contentSection)
    {
        $contentSection->delete();

        return redirect()->route('admin.content-sections.index')
            ->with('success', 'Content section deleted successfully.');
    }

    /**
     * Toggle the active status of a content section.
     */
    public function toggleStatus(SurveyContentSection $contentSection)
    {
        $contentSection->update(['is_active' => !$contentSection->is_active]);

        $status = $contentSection->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Content section {$status} successfully.");
    }

    /**
     * API endpoint to get subcategories for a category.
     */
    public function getSubcategories(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        if (!$categoryId) {
            return response()->json(['subcategories' => []]);
        }

        $subcategories = SurveySubcategory::where('category_id', $categoryId)
            ->active()
            ->ordered()
            ->get(['id', 'display_name']);

        return response()->json(['subcategories' => $subcategories]);
    }
}
