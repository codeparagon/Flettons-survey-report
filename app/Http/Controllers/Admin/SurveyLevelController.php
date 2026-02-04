<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyLevel;
use App\Models\SurveySectionDefinition;
use App\Models\SurveyAccommodationType;
use App\Models\SurveyContentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyLevelController extends Controller
{
    /**
     * Display a listing of survey levels.
     */
    public function index()
    {
        $levels = SurveyLevel::with('sectionDefinitions')->ordered()->paginate(15);
        
        return view('admin.survey-levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new survey level.
     */
    public function create()
    {
        $sections = SurveySectionDefinition::active()->ordered()->with('subcategory.category')->get();
        $accommodationTypes = SurveyAccommodationType::active()->ordered()->get();
        $contentSections = SurveyContentSection::active()->ordered()->get();
        
        return view('admin.survey-levels.create', compact('sections', 'accommodationTypes', 'contentSections'));
    }

    /**
     * Store a newly created survey level.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_levels,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sections' => 'nullable|array',
            'sections.*' => 'exists:survey_section_definitions,id',
            'accommodation_types' => 'nullable|array',
            'accommodation_types.*' => 'exists:survey_accommodation_types,id',
            'content_sections' => 'nullable|array',
            'content_sections.*' => 'exists:survey_content_sections,id',
        ]);

        $level = SurveyLevel::create($validated);

        // Attach sections if provided
        if (isset($validated['sections'])) {
            $sectionsWithOrder = [];
            foreach ($validated['sections'] as $index => $sectionId) {
                $sectionsWithOrder[$sectionId] = ['sort_order' => $index + 1];
            }
            $level->sectionDefinitions()->attach($sectionsWithOrder);
        }
        
        // Attach accommodation types if provided
        if (isset($validated['accommodation_types'])) {
            foreach ($validated['accommodation_types'] as $index => $typeId) {
                DB::table('survey_level_accommodation_types')->insert([
                    'survey_level_id' => $level->id,
                    'accommodation_type_id' => $typeId,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Attach content sections if provided
        if (isset($validated['content_sections'])) {
            foreach ($validated['content_sections'] as $index => $sectionId) {
                DB::table('survey_level_content_sections')->insert([
                    'survey_level_id' => $level->id,
                    'content_section_id' => $sectionId,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.survey-levels.index')
            ->with('success', 'Survey level created successfully.');
    }

    /**
     * Display the specified survey level.
     */
    public function show(SurveyLevel $surveyLevel)
    {
        $surveyLevel->load([
            'sectionDefinitions.subcategory.category', 
            'accommodationTypes',
            'contentSections',
            'surveys'
        ]);
        
        return view('admin.survey-levels.show', compact('surveyLevel'));
    }

    /**
     * Show the form for editing the specified survey level.
     */
    public function edit(SurveyLevel $surveyLevel)
    {
        $sections = SurveySectionDefinition::active()->ordered()->with('subcategory.category')->get();
        $selectedSections = $surveyLevel->sectionDefinitions->pluck('id')->toArray();
        
        $accommodationTypes = SurveyAccommodationType::active()->ordered()->get();
        $selectedAccommodationTypes = $surveyLevel->accommodationTypes->pluck('id')->toArray();
        
        $contentSections = SurveyContentSection::active()->ordered()->get();
        $selectedContentSections = $surveyLevel->contentSections->pluck('id')->toArray();
        
        return view('admin.survey-levels.edit', compact(
            'surveyLevel', 
            'sections', 
            'selectedSections',
            'accommodationTypes',
            'selectedAccommodationTypes',
            'contentSections',
            'selectedContentSections'
        ));
    }

    /**
     * Update the specified survey level.
     */
    public function update(Request $request, SurveyLevel $surveyLevel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_levels,name,' . $surveyLevel->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sections' => 'nullable|array',
            'sections.*' => 'exists:survey_section_definitions,id',
            'accommodation_types' => 'nullable|array',
            'accommodation_types.*' => 'exists:survey_accommodation_types,id',
            'content_sections' => 'nullable|array',
            'content_sections.*' => 'exists:survey_content_sections,id',
        ]);

        $surveyLevel->update($validated);

        // Update sections
        if (isset($validated['sections'])) {
            $sectionsWithOrder = [];
            foreach ($validated['sections'] as $index => $sectionId) {
                $sectionsWithOrder[$sectionId] = ['sort_order' => $index + 1];
            }
            $surveyLevel->sectionDefinitions()->sync($sectionsWithOrder);
        } else {
            $surveyLevel->sectionDefinitions()->detach();
        }
        
        // Update accommodation types
        DB::table('survey_level_accommodation_types')
            ->where('survey_level_id', $surveyLevel->id)
            ->delete();
        if (isset($validated['accommodation_types'])) {
            foreach ($validated['accommodation_types'] as $index => $typeId) {
                DB::table('survey_level_accommodation_types')->insert([
                    'survey_level_id' => $surveyLevel->id,
                    'accommodation_type_id' => $typeId,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Update content sections
        DB::table('survey_level_content_sections')
            ->where('survey_level_id', $surveyLevel->id)
            ->delete();
        if (isset($validated['content_sections'])) {
            foreach ($validated['content_sections'] as $index => $sectionId) {
                DB::table('survey_level_content_sections')->insert([
                    'survey_level_id' => $surveyLevel->id,
                    'content_section_id' => $sectionId,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.survey-levels.index')
            ->with('success', 'Survey level updated successfully.');
    }

    /**
     * Remove the specified survey level.
     */
    public function destroy(SurveyLevel $surveyLevel)
    {
        // Check if level has surveys
        if ($surveyLevel->surveys()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete level that has surveys. Please reassign surveys first.');
        }

        // Detach sections, accommodation types, and content sections first
        $surveyLevel->sectionDefinitions()->detach();
        DB::table('survey_level_accommodation_types')
            ->where('survey_level_id', $surveyLevel->id)
            ->delete();
        DB::table('survey_level_content_sections')
            ->where('survey_level_id', $surveyLevel->id)
            ->delete();
        
        $surveyLevel->delete();

        return redirect()->route('admin.survey-levels.index')
            ->with('success', 'Survey level deleted successfully.');
    }

    /**
     * Toggle the active status of a survey level.
     */
    public function toggleStatus(SurveyLevel $surveyLevel)
    {
        $surveyLevel->update(['is_active' => !$surveyLevel->is_active]);

        $status = $surveyLevel->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Survey level {$status} successfully.");
    }
}
