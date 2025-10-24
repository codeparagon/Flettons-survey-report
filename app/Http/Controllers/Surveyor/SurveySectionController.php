<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveySection;
use App\Models\SurveySectionAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SurveySectionController extends Controller
{
    /**
     * Show survey categories for a specific survey.
     */
    public function showCategories(Survey $survey)
    {
        // Check if surveyor can access this survey
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $categories = \App\Models\SurveyCategory::getCategoriesWithSectionsForLevel($survey->level);
        $assessments = $survey->sectionAssessments()->with('section')->get()->keyBy('survey_section_id');
        $progress = $survey->getCompletionProgress();

        return view('surveyor.survey.categories', compact('survey', 'categories', 'assessments', 'progress'));
    }

    /**
     * Show sections for a specific category.
     */
    public function showCategorySections(Survey $survey, \App\Models\SurveyCategory $category)
    {
        // Check if surveyor can access this survey
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Get sections for this category and survey level
        $sections = $category->sections()
            ->whereIn('name', $this->getSectionNamesForLevel($survey->level))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($sections->isEmpty()) {
            abort(404, 'No sections found for this category and survey level');
        }

        $assessments = $survey->sectionAssessments()->with('section')->get()->keyBy('survey_section_id');

        return view('surveyor.survey.category-sections', compact('survey', 'category', 'sections', 'assessments'));
    }

    /**
     * Show survey sections for a specific survey (legacy method - redirects to categories).
     */
    public function showSections(Survey $survey)
    {
        // Redirect to the new category-based flow
        return redirect()->route('surveyor.survey.categories', $survey);
    }

    /**
     * Get section names for a specific survey level.
     */
    private function getSectionNamesForLevel($level)
    {
        $surveyLevel = \App\Models\SurveyLevel::where('name', $level)->first();
        
        if (!$surveyLevel) {
            return [];
        }
        
        return $surveyLevel->getSectionNames();
    }

    /**
     * Show form for a specific section assessment.
     */
    public function showSectionForm(Survey $survey, SurveySection $section)
    {
        // Check if surveyor can access this survey
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if this section is required for this survey level
        $requiredSections = $survey->getRequiredSections();
        if (!$requiredSections->contains('id', $section->id)) {
            abort(404, 'Section not required for this survey level');
        }

        // Get existing assessment or create new one
        $assessment = $survey->sectionAssessments()
            ->where('survey_section_id', $section->id)
            ->first();

        if (!$assessment) {
            $assessment = new SurveySectionAssessment([
                'survey_id' => $survey->id,
                'survey_section_id' => $section->id,
                'is_completed' => false,
            ]);
        }

        return view('surveyor.survey.section-form', compact('survey', 'section', 'assessment'));
    }

    /**
     * Save section assessment.
     */
    public function saveSectionAssessment(Request $request, Survey $survey, SurveySection $section)
    {
        // Check if surveyor can access this survey
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'condition_rating' => 'required|in:excellent,good,fair,poor',
            'defects_noted' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'photos.*' => 'nullable|image|max:5120', // 5MB max per photo
            'additional_data' => 'nullable|array',
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("surveys/{$survey->id}/{$section->name}", 'public');
                $photoPaths[] = $path;
            }
        }

        // Get existing photos and merge with new ones
        $existingAssessment = $survey->sectionAssessments()
            ->where('survey_section_id', $section->id)
            ->first();

        $existingPhotos = $existingAssessment ? ($existingAssessment->photos ?? []) : [];
        $allPhotos = array_merge($existingPhotos, $photoPaths);

        // Update or create assessment
        $assessment = SurveySectionAssessment::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'survey_section_id' => $section->id,
            ],
            [
                'condition_rating' => $validated['condition_rating'],
                'defects_noted' => $validated['defects_noted'],
                'recommendations' => $validated['recommendations'],
                'notes' => $validated['notes'],
                'photos' => $allPhotos,
                'additional_data' => $validated['additional_data'] ?? [],
                'is_completed' => true,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('surveyor.survey.sections', $survey)
            ->with('success', "{$section->display_name} assessment saved successfully!");
    }

    /**
     * Delete a photo from assessment.
     */
    public function deletePhoto(Request $request, Survey $survey, SurveySection $section)
    {
        // Check if surveyor can access this survey
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'photo_path' => 'required|string',
        ]);

        $assessment = $survey->sectionAssessments()
            ->where('survey_section_id', $section->id)
            ->first();

        if ($assessment && $assessment->photos) {
            $photos = $assessment->photos;
            $photoToDelete = $request->photo_path;

            // Remove photo from array
            $photos = array_filter($photos, function($photo) use ($photoToDelete) {
                return $photo !== $photoToDelete;
            });

            // Delete file from storage
            if (Storage::disk('public')->exists($photoToDelete)) {
                Storage::disk('public')->delete($photoToDelete);
            }

            // Update assessment
            $assessment->update(['photos' => array_values($photos)]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark section as incomplete.
     */
    public function markIncomplete(Survey $survey, SurveySection $section)
    {
        // Check if surveyor can access this survey
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $assessment = $survey->sectionAssessments()
            ->where('survey_section_id', $section->id)
            ->first();

        if ($assessment) {
            $assessment->update([
                'is_completed' => false,
                'completed_at' => null,
                'completed_by' => null,
            ]);
        }

        return redirect()
            ->route('surveyor.survey.sections', $survey)
            ->with('success', "{$section->display_name} marked as incomplete");
    }
}