<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveySectionAssessment;
use App\Models\Survey;
use App\Models\SurveySection;
use Illuminate\Http\Request;

class SurveySectionAssessmentController extends Controller
{
    /**
     * Display a listing of survey section assessments.
     */
    public function index()
    {
        $assessments = SurveySectionAssessment::with(['survey', 'section'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.survey-section-assessments.index', compact('assessments'));
    }

    /**
     * Display the specified survey section assessment.
     */
    public function show(SurveySectionAssessment $assessment)
    {
        $assessment->load(['survey', 'section', 'completedBy']);
        
        return view('admin.survey-section-assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified survey section assessment.
     */
    public function edit(SurveySectionAssessment $assessment)
    {
        $assessment->load(['survey', 'section']);
        
        return view('admin.survey-section-assessments.edit', compact('assessment'));
    }

    /**
     * Update the specified survey section assessment.
     */
    public function update(Request $request, SurveySectionAssessment $assessment)
    {
        $validated = $request->validate([
            'condition_rating' => 'required|in:excellent,good,fair,poor',
            'defects_noted' => 'nullable|string|max:2000',
            'recommendations' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'is_completed' => 'boolean',
        ]);

        $assessment->update($validated);

        return redirect()->route('admin.survey-section-assessments.index')
            ->with('success', 'Assessment updated successfully.');
    }

    /**
     * Remove the specified survey section assessment.
     */
    public function destroy(SurveySectionAssessment $assessment)
    {
        // Delete associated photos
        if ($assessment->photos) {
            foreach ($assessment->photos as $photo) {
                if (\Storage::disk('public')->exists($photo)) {
                    \Storage::disk('public')->delete($photo);
                }
            }
        }

        $assessment->delete();

        return redirect()->route('admin.survey-section-assessments.index')
            ->with('success', 'Assessment deleted successfully.');
    }

    /**
     * Toggle the completion status of an assessment.
     */
    public function toggleCompletion(SurveySectionAssessment $assessment)
    {
        $assessment->update([
            'is_completed' => !$assessment->is_completed,
            'completed_at' => $assessment->is_completed ? null : now(),
            'completed_by' => $assessment->is_completed ? null : auth()->id(),
        ]);

        $status = $assessment->is_completed ? 'marked as incomplete' : 'marked as complete';
        
        return redirect()->back()
            ->with('success', "Assessment {$status} successfully.");
    }

    /**
     * Delete a photo from an assessment.
     */
    public function deletePhoto(Request $request, SurveySectionAssessment $assessment)
    {
        $request->validate([
            'photo_path' => 'required|string',
        ]);

        if ($assessment->photos) {
            $photos = $assessment->photos;
            $photoToDelete = $request->photo_path;

            // Remove photo from array
            $photos = array_filter($photos, function($photo) use ($photoToDelete) {
                return $photo !== $photoToDelete;
            });

            // Delete file from storage
            if (\Storage::disk('public')->exists($photoToDelete)) {
                \Storage::disk('public')->delete($photoToDelete);
            }

            // Update assessment
            $assessment->update(['photos' => array_values($photos)]);
        }

        return response()->json(['success' => true]);
    }
}


