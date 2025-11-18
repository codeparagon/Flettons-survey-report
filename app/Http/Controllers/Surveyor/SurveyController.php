<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyNote;
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

        return redirect()
            ->route('surveyor.surveys.show', $survey)
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

    /**
     * Temporary method to show new detail design
     */
    public function detail(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('surveyor.surveys.detail', compact('survey'));
    }

    /**
     * Mock survey detail screen for UI build
     */
    public function detailMock(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Load relationships if needed
        $survey->load('surveyor');

        return view('surveyor.surveys.mocks.detail', compact('survey'));
    }

    public function surveyDetails($id)
    {
        $data = [
            'survey' => Survey::find($id),
        ];
        return view('surveyor.surveys.mocks.detail', $data);
    }

    /**
     * Mock desk study screen for UI build
     */
    public function deskStudyMock(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Build desk study data from survey or use defaults
        $deskStudy = [
            'address' => $survey->full_address ?? '123, Sample Street, Kent DA9 9ZT',
            'job_reference' => $survey->job_reference ?? '12SE39DT-SH',
            'map' => [
                'image' => 'https://images.pexels.com/photos/439391/pexels-photo-439391.jpeg?auto=compress&cs=tinysrgb&w=800',
                'longitude' => '-0.3112',
                'latitude' => '51.4728',
            ],
            'flood_risks' => [
                ['label' => 'Rivers and Seas', 'value' => 'Very Low'],
                ['label' => 'Surface Water', 'value' => 'Low'],
                ['label' => 'Reservoirs', 'value' => 'Yes'],
                ['label' => 'Ground Water', 'value' => 'No'],
            ],
            'planning' => [
                ['label' => 'Council Tax', 'value' => 'Band C'],
                ['label' => 'EPC Rating', 'value' => 'D'],
                ['label' => 'Soil Type', 'value' => 'Soilscope 7 (High Risk)'],
                ['label' => 'Listed Building', 'value' => $survey->listed_building ?? 'N/A'],
                ['label' => 'Conservation Area', 'value' => 'Yes'],
                ['label' => 'Article 4', 'value' => 'No'],
            ],
        ];

        return view('surveyor.surveys.mocks.desk_study', compact('survey', 'deskStudy'));
    }

    public function dataMock(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Load section assessments if needed
        $survey->load('sectionAssessments.section');

        return view('surveyor.surveys.mocks.data', compact('survey'));
    }

    public function mediaMock(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('surveyor.surveys.mocks.media', compact('survey'));
    }

    public function transcriptMock(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // For now, use mock transcript data. Later this can be fetched from database
        $transcript = [
            [
                'time' => '00:00:12',
                'speaker' => 'Surveyor',
                'text' => 'Arrived on site and introduced myself to the occupant. Confirmed access to the loft and rear garden.',
            ],
            [
                'time' => '00:03:45',
                'speaker' => 'Occupant',
                'text' => 'Highlighted previous damp issue along the rear elevation and recent roof repairs.',
            ],
            [
                'time' => '00:08:10',
                'speaker' => 'Surveyor',
                'text' => 'Noted cracked render to rear ground floor wall, moisture readings elevated around 22%.',
            ],
        ];

        return view('surveyor.surveys.mocks.transcript', compact('survey', 'transcript'));
    }

    public function documentsMock(Survey $survey)
    {
        // Surveyor can view:
        // 1. Surveys assigned to them
        // 2. Unassigned surveys (to claim them)
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // For now, use mock documents data. Later this can be fetched from database
        $documents = [
            ['name' => 'Lease Agreement.pdf', 'uploaded_at' => '10 Oct 2025', 'size' => '1.2 MB'],
            ['name' => 'Planning Consent.pdf', 'uploaded_at' => '08 Oct 2025', 'size' => '850 KB'],
            ['name' => 'Previous Survey.jpg', 'uploaded_at' => '05 Oct 2025', 'size' => '2.4 MB'],
        ];

        return view('surveyor.surveys.mocks.documents', compact('survey', 'documents'));
    }

    public function createNewSurvey(Request $request)
    {
        Survey::create([
            'surveyor_id' => auth()->id(),
            'level' => $request->level,
            'scheduled_date' => $request->scheduled_date,
            'full_address' => $request->full_address,
            'postcode' => $request->postcode,
            'job_reference' => $request->job_reference,
            'house_or_flat' => $request->house_or_flat,
            'listed_building' => $request->listed_building,
            'number_of_bedrooms' => $request->number_of_bedrooms,
            'receptions' => $request->receptions,
            'bathrooms' => $request->bathrooms,
        ]);

        return redirect()->back()->with('success', 'New Survey Created Successfully.');
    }

    public function updateSurvey(Request $request)
    {
        try {
            if ($request->field_type == 'notes') {
                $notes = SurveyNote::find($request->note_id);
                $notes->update([
                    'note' => $request->notes,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Survey note updated successfully.']);
            }
            $survey = Survey::find($request->survey_id);
            if ($request->field == 'client_name') {
                $parts = explode(' ', trim($request->value));
                $first_name = $parts[0];
                $last_name = implode(' ', array_slice($parts, 1));
                $survey->update([
                    'first_name'=> $first_name,
                    'last_name' => $last_name ?? ' ',
                ]);
                return response()->json($survey);
                return response()->json(['status' => 'success', 'message' => 'Survey updated successfully.']);
            }

            $survey->update([
                $request->field => $request->value,
            ]);
            return response()->json($survey);
            return response()->json(['status' => 'success', 'message' => 'Survey updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update survey.']);
        }
    }

    public function addSurveyNote(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'notes' => 'required|string',
        ]);

        SurveyNote::create([
            'created_by' => auth()->id(),
            'survey_id' => $request->survey_id,
            'note' => $request->notes,
            'dated_at' => $request->dated_at,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Note added successfully.']);
    }
}
