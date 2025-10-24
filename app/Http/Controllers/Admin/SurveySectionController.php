<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveySection;
use App\Models\SurveyCategory;
use Illuminate\Http\Request;

class SurveySectionController extends Controller
{
    /**
     * Display a listing of survey sections.
     */
    public function index()
    {
        $sections = SurveySection::with('category')->ordered()->paginate(15);
        
        return view('admin.survey-sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new survey section.
     */
    public function create()
    {
        $categories = SurveyCategory::active()->ordered()->get();
        
        return view('admin.survey-sections.create', compact('categories'));
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
        ]);

        // Handle icon file upload
        if ($request->hasFile('icon_file')) {
            $iconPath = $request->file('icon_file')->store('icons/sections', 'public');
            $validated['icon'] = 'storage/' . $iconPath;
        } else {
            // Use custom icon if provided, otherwise use selected icon
            $validated['icon'] = $validated['custom_icon'] ?: $validated['icon'];
        }

        SurveySection::create($validated);

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
        
        return view('admin.survey-sections.edit', compact('surveySection', 'categories'));
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
            $validated['icon'] = 'storage/' . $iconPath;
        } else {
            // Use custom icon if provided, otherwise use selected icon
            $validated['icon'] = $validated['custom_icon'] ?: $validated['icon'];
        }

        $surveySection->update($validated);

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
}