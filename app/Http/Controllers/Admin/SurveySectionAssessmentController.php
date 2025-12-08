<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveySectionAssessment;
use App\Models\Survey;
use App\Models\SurveySectionDefinition;
use Illuminate\Http\Request;

class SurveySectionAssessmentController extends Controller
{
    /**
     * Display a listing of survey section assessments.
     */
    public function index()
    {
        $assessments = SurveySectionAssessment::with(['survey', 'sectionDefinition.subcategory.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.survey-section-assessments.index', compact('assessments'));
    }

    /**
     * Display the specified survey section assessment.
     */
    public function show(SurveySectionAssessment $assessment)
    {
        $assessment->load(['survey', 'sectionDefinition.subcategory.category', 'completedBy', 'photos', 'costs', 'defects']);
        
        return view('admin.survey-section-assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified survey section assessment.
     */
    public function edit(SurveySectionAssessment $assessment)
    {
        $assessment->load(['survey', 'sectionDefinition.subcategory.category']);
        
        return view('admin.survey-section-assessments.edit', compact('assessment'));
    }

    /**
     * Update the specified survey section assessment.
     */
    public function update(Request $request, SurveySectionAssessment $assessment)
    {
        $validated = $request->validate([
            'condition_rating' => 'nullable|integer|in:1,2,3',
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
        foreach ($assessment->photos as $photo) {
            if (\Storage::disk('public')->exists($photo->file_path)) {
                \Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }

        // Delete costs
        $assessment->costs()->delete();
        
        // Detach defects
        $assessment->defects()->detach();

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
            'photo_id' => 'required|integer',
        ]);

        $photo = $assessment->photos()->find($request->photo_id);
        
        if ($photo) {
            if (\Storage::disk('public')->exists($photo->file_path)) {
                \Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }

        return response()->json(['success' => true]);
    }
}
