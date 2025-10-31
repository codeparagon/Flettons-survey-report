<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveySection;
use App\Models\SurveySectionAssessment;
use App\Services\SectionAssessmentService;
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
     * Get all sections for a survey grouped by category with completion status.
     */
    private function getSectionsHierarchy(Survey $survey)
    {
        $categories = \App\Models\SurveyCategory::getCategoriesWithSectionsForLevel($survey->level);
        $assessments = $survey->sectionAssessments()->with('section')->get()->keyBy('survey_section_id');
        
        return $categories->map(function($category) use ($assessments) {
            $sections = $category->sections->map(function($section) use ($assessments) {
                $assessment = $assessments->get($section->id);
                return [
                    'id' => $section->id,
                    'model' => $section, // Include model for route binding
                    'name' => $section->name,
                    'display_name' => $section->display_name,
                    'description' => $section->description,
                    'icon' => $section->icon,
                    'is_completed' => $assessment ? $assessment->is_completed : false,
                    'sort_order' => $section->sort_order,
                ];
            })->sortBy('sort_order')->values();
            
            return [
                'id' => $category->id,
                'name' => $category->name,
                'display_name' => $category->display_name,
                'description' => $category->description,
                'icon' => $category->icon,
                'sections' => $sections,
            ];
        })->filter(function($category) {
            // Only include categories that have at least one section
            return $category['sections']->count() > 0;
        })->values();
    }

    /**
     * Get next incomplete section after the current section.
     */
    private function getNextSection(Survey $survey, SurveySection $currentSection)
    {
        $requiredSections = $survey->getRequiredSections()->sortBy('sort_order')->values();
        $assessments = $survey->sectionAssessments()->where('is_completed', true)->pluck('survey_section_id')->toArray();
        
        $foundCurrent = false;
        
        foreach ($requiredSections as $section) {
            if ($foundCurrent && !in_array($section->id, $assessments)) {
                return $section;
            }
            
            if ($section->id === $currentSection->id) {
                $foundCurrent = true;
            }
        }
        
        return null;
    }

    /**
     * Get previous section before the current section.
     */
    private function getPreviousSection(Survey $survey, SurveySection $currentSection)
    {
        $requiredSections = $survey->getRequiredSections()->sortBy('sort_order')->values();
        $previousSection = null;
        
        foreach ($requiredSections as $section) {
            if ($section->id === $currentSection->id) {
                return $previousSection;
            }
            $previousSection = $section;
        }
        
        return null;
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

        // Load section with fields for dynamic form rendering
        $section->load('fields');

        // Get hierarchy for sidebar
        $hierarchy = $this->getSectionsHierarchy($survey);
        $progress = $survey->getCompletionProgress();
        
        // Get next/previous sections for navigation
        $nextSection = $this->getNextSection($survey, $section);
        $previousSection = $this->getPreviousSection($survey, $section);

        return view('surveyor.survey.section-form', compact('survey', 'section', 'assessment', 'hierarchy', 'progress', 'nextSection', 'previousSection'));
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

        $sectionService = new SectionAssessmentService();
        
        // Build validation rules dynamically
        $validationRules = $sectionService->buildValidationRules($section);

        // Validate the request
        $validated = $request->validate($validationRules);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("surveys/{$survey->id}/{$section->name}", 'public');
                $photoPaths[] = $path;
            }
        }

        // Extract field values if using custom fields
        $section->refresh();
        $section->load(['fields' => function($query) {
            $query->where('is_active', true);
        }]);
        $customFields = $section->fields;
        
        $assessmentData = [
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ];

        if ($customFields && $customFields->count() > 0) {
            // Store custom field values in additional_data
            $fieldValues = $sectionService->extractFieldValues($request, $section);
            $assessmentData['additional_data'] = $fieldValues;
            
            // Handle photos for custom fields too
            $existingPhotosInput = $request->input('existing_photos', []);
            $existingPhotos = is_array($existingPhotosInput) ? $existingPhotosInput : [];
            $assessmentData['photos'] = array_merge($existingPhotos, $photoPaths);
            
            // Set condition_rating from first rating field if exists, or null
            $ratingField = $customFields->firstWhere('field_type', 'rating');
            if ($ratingField && isset($validated['field_' . $ratingField->id])) {
                $assessmentData['condition_rating'] = $validated['field_' . $ratingField->id];
            } else {
                $assessmentData['condition_rating'] = null;
            }
        } else {
            // Use new default fields structure
            $assessmentData['report_content'] = $validated['report_content'] ?? null;
            $assessmentData['material'] = $validated['material'] ?? null;
            
            // Handle defects - ensure it's an array
            $defects = $request->input('defects', []);
            
            // If defects is not an array, try to convert it
            if (!is_array($defects)) {
                if (is_string($defects)) {
                    $defects = json_decode($defects, true);
                }
                if (!is_array($defects)) {
                    $defects = [];
                }
            }
            
            // Filter out empty values and ensure array format
            $defects = array_values(array_filter($defects, function($value) {
                return !empty($value) && $value !== '';
            }));
            
            $assessmentData['defects'] = $defects;
            
            $assessmentData['remaining_life'] = $validated['remaining_life'] ?? null;
            $assessmentData['notes'] = $validated['notes'] ?? null;
            
            // Handle photos: preserve existing photos (that weren't deleted) + add new ones
            $existingPhotosInput = $request->input('existing_photos', []);
            $existingPhotos = is_array($existingPhotosInput) ? $existingPhotosInput : [];
            $assessmentData['photos'] = array_merge($existingPhotos, $photoPaths);
        }

        // Update or create assessment
        $assessment = SurveySectionAssessment::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'survey_section_id' => $section->id,
            ],
            $assessmentData
        );

        // Get next incomplete section for auto-navigation
        $nextSection = $this->getNextSection($survey, $section);
        
        if ($nextSection) {
            return redirect()
                ->route('surveyor.survey.section.form', [$survey, $nextSection])
                ->with('success', "{$section->display_name} saved! Moving to next section: {$nextSection->display_name}");
        } else {
            // All sections completed - go to categories summary
        return redirect()
                ->route('surveyor.survey.categories', $survey)
                ->with('success', "🎉 All sections completed!");
        }
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