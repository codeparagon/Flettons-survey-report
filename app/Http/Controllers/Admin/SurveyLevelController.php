<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyLevel;
use App\Models\SurveySection;
use Illuminate\Http\Request;

class SurveyLevelController extends Controller
{
    /**
     * Display a listing of survey levels.
     */
    public function index()
    {
        $levels = SurveyLevel::ordered()->paginate(15);
        
        return view('admin.survey-levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new survey level.
     */
    public function create()
    {
        $sections = SurveySection::active()->ordered()->get();
        
        return view('admin.survey-levels.create', compact('sections'));
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
            'sections.*' => 'exists:survey_sections,id',
        ]);

        $level = SurveyLevel::create($validated);

        // Attach sections if provided
        if (isset($validated['sections'])) {
            $sectionsWithOrder = [];
            foreach ($validated['sections'] as $index => $sectionId) {
                $sectionsWithOrder[$sectionId] = ['sort_order' => $index + 1];
            }
            $level->sections()->attach($sectionsWithOrder);
        }

        return redirect()->route('admin.survey-levels.index')
            ->with('success', 'Survey level created successfully.');
    }

    /**
     * Display the specified survey level.
     */
    public function show(SurveyLevel $surveyLevel)
    {
        $surveyLevel->load(['sections.category', 'surveys']);
        
        return view('admin.survey-levels.show', compact('surveyLevel'));
    }

    /**
     * Show the form for editing the specified survey level.
     */
    public function edit(SurveyLevel $surveyLevel)
    {
        $sections = SurveySection::active()->ordered()->get();
        $selectedSections = $surveyLevel->sections->pluck('id')->toArray();
        
        return view('admin.survey-levels.edit', compact('surveyLevel', 'sections', 'selectedSections'));
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
            'sections.*' => 'exists:survey_sections,id',
        ]);

        $surveyLevel->update($validated);

        // Update sections
        if (isset($validated['sections'])) {
            $sectionsWithOrder = [];
            foreach ($validated['sections'] as $index => $sectionId) {
                $sectionsWithOrder[$sectionId] = ['sort_order' => $index + 1];
            }
            $surveyLevel->sections()->sync($sectionsWithOrder);
        } else {
            $surveyLevel->sections()->detach();
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

        // Detach sections first
        $surveyLevel->sections()->detach();
        
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