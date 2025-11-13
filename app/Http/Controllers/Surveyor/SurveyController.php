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
    public function detailMock()
    {
        $survey = (object) [
            'full_address' => '123, Sample Street, Kent DA9 9ZT',
            'job_reference' => '12SE39DT-SH',
            'client_name' => 'Anthony',
            'client_email' => 'Anthony@hotmail.com',
            'client_phone' => '07901333164',
            'client_address' => '66 Home Road, Kent',
            'property_type' => 'House',
            'estate_holding' => 'Freehold',
            'access_contact' => 'Anthony',
            'access_role' => 'Vendor',
            'postcode' => 'DA9 9XZ',
            'beds' => 2,
            'baths' => 2,
            'receptions' => 1,
            'garage' => 2,
            'wc' => 0,
            'utility' => 2,
            'garden' => 'Y',
        ];

        return view('surveyor.surveys.detail', compact('survey'));
    }

    /**
     * Mock desk study screen for UI build
     */
    public function deskStudyMock()
    {
        $deskStudy = [
            'address' => '123, Sample Street, Kent DA9 9ZT',
            'job_reference' => '12SE39DT-SH',
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
                ['label' => 'Listed Building', 'value' => 'N/A'],
                ['label' => 'Conservation Area', 'value' => 'Yes'],
                ['label' => 'Article 4', 'value' => 'No'],
            ],
        ];

        return view('surveyor.surveys.desk_study', compact('deskStudy'));
    }

    public function dataMock()
    {
        return view('surveyor.surveys.data_mock');
    }

    public function mediaMock()
    {
        return view('surveyor.surveys.media_mock');
    }

    public function transcriptMock()
    {
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

        return view('surveyor.surveys.transcript_mock', compact('transcript'));
    }

    public function documentsMock()
    {
        $documents = [
            ['name' => 'Lease Agreement.pdf', 'uploaded_at' => '10 Oct 2025', 'size' => '1.2 MB'],
            ['name' => 'Planning Consent.pdf', 'uploaded_at' => '08 Oct 2025', 'size' => '850 KB'],
            ['name' => 'Previous Survey.jpg', 'uploaded_at' => '05 Oct 2025', 'size' => '2.4 MB'],
        ];

        return view('surveyor.surveys.documents_mock', compact('documents'));
    }


    public function createNewSurvey(Request $request){
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

}



