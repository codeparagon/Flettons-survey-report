<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyCategory;
use Illuminate\Http\Request;

class SurveyCategoryController extends Controller
{
    /**
     * Display a listing of survey categories.
     */
    public function index()
    {
        $categories = SurveyCategory::ordered()->paginate(15);
        
        return view('admin.survey-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new survey category.
     */
    public function create()
    {
        return view('admin.survey-categories.create');
    }

    /**
     * Store a newly created survey category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_categories,name',
            'display_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'custom_icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Use custom icon if provided, otherwise use selected icon
        $validated['icon'] = $validated['custom_icon'] ?: $validated['icon'];

        SurveyCategory::create($validated);

        return redirect()->route('admin.survey-categories.index')
            ->with('success', 'Survey category created successfully.');
    }

    /**
     * Display the specified survey category.
     */
    public function show(SurveyCategory $surveyCategory)
    {
        $surveyCategory->load('sections');
        
        return view('admin.survey-categories.show', compact('surveyCategory'));
    }

    /**
     * Show the form for editing the specified survey category.
     */
    public function edit(SurveyCategory $surveyCategory)
    {
        return view('admin.survey-categories.edit', compact('surveyCategory'));
    }

    /**
     * Update the specified survey category.
     */
    public function update(Request $request, SurveyCategory $surveyCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_categories,name,' . $surveyCategory->id,
            'display_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'custom_icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Use custom icon if provided, otherwise use selected icon
        $validated['icon'] = $validated['custom_icon'] ?: $validated['icon'];

        $surveyCategory->update($validated);

        return redirect()->route('admin.survey-categories.index')
            ->with('success', 'Survey category updated successfully.');
    }

    /**
     * Remove the specified survey category.
     */
    public function destroy(SurveyCategory $surveyCategory)
    {
        // Check if category has sections
        if ($surveyCategory->sections()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that has sections. Please delete or reassign sections first.');
        }

        $surveyCategory->delete();

        return redirect()->route('admin.survey-categories.index')
            ->with('success', 'Survey category deleted successfully.');
    }

    /**
     * Toggle the active status of a survey category.
     */
    public function toggleStatus(SurveyCategory $surveyCategory)
    {
        $surveyCategory->update(['is_active' => !$surveyCategory->is_active]);

        $status = $surveyCategory->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Survey category {$status} successfully.");
    }
}
