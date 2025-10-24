<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        // Get assigned surveys
        $assignedSurveys = Survey::where('surveyor_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get unassigned surveys that can be claimed
        $unassignedSurveys = Survey::whereNull('surveyor_id')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('surveyor.surveys.index', compact('assignedSurveys', 'unassignedSurveys'));
    }

    public function show(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        return view('surveyor.surveys.show', compact('survey'));
    }

    public function updateStatus(Request $request, Survey $survey)
    {
        // Surveyor can only update their own surveys
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:assigned,in_progress,completed',
        ]);

        $survey->update($validated);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Claim an unassigned survey (self-assign).
     */
    public function claim(Survey $survey)
    {
        if ($survey->surveyor_id) {
            return redirect()->back()->with('error', 'This survey is already assigned.');
        }

        $survey->update([
            'surveyor_id' => auth()->id(),
            'status' => $survey->status === 'pending' ? 'assigned' : $survey->status,
        ]);

        return redirect()->route('surveyor.surveys.show', $survey)
            ->with('success', 'Survey claimed successfully.');
    }

    /**
     * Start a survey: mark in_progress and go to sections page.
     */
    public function start(Survey $survey)
    {
        // Only assigned surveyor can start
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (in_array($survey->status, ['pending', 'assigned'])) {
            $survey->update(['status' => 'in_progress']);
        }

        return redirect()->route('surveyor.survey.categories', $survey);
    }

}



