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
        $detail = [
            'address' => '123, Sample Street, Kent DA9 9ZT',
            'job_reference' => '12SE39DT-SH',
            'client' => [
                'full_name' => 'Anthony',
                'email' => 'Anthony@hotmail.com',
                'phone' => '07901333164',
                'home_address' => '66 Home Road, Kent',
                'concerns' => 'I want the surveyor to check the roof and guttering thoroughly to confirm the extent of any leaks or damp patches that we have seen during heavy rain.',
            ],
            'property' => [
                'full_address' => '66 Sample Street, Kent',
                'postcode' => 'DA9 9XZ',
                'access_contact' => 'Anthony',
                'access_role' => 'Vendor',
                'access_role_options' => ['Vendor', 'Agent', 'Owner', 'Tenant'],
                'type' => 'House',
                'type_options' => ['House', 'Flat', 'Bungalow', 'Cottage', 'Maisonette'],
                'stats' => [
                    ['label' => 'Beds', 'value' => 2, 'name' => 'beds', 'type' => 'number', 'min' => 0],
                    ['label' => 'Baths', 'value' => 2, 'name' => 'baths', 'type' => 'number', 'min' => 0],
                    ['label' => 'Receptions', 'value' => 1, 'name' => 'receptions', 'type' => 'number', 'min' => 0],
                    ['label' => 'Garage', 'value' => 2, 'name' => 'garage', 'type' => 'number', 'min' => 0],
                    ['label' => 'WC', 'value' => 0, 'name' => 'wc', 'type' => 'number', 'min' => 0],
                    ['label' => 'Utility', 'value' => 2, 'name' => 'utility', 'type' => 'number', 'min' => 0],
                    [
                        'label' => 'Garden',
                        'value' => 'Y',
                        'name' => 'garden',
                        'type' => 'select',
                        'options' => ['Y' => 'Yes', 'N' => 'No']
                    ],
                ],
            ],
            'case_notes' => [
                [
                    'timestamp' => '10/10/2025 · 5:00pm',
                    'body' => 'Spoke to the customer and advised that the property has damp and will require extensive work.',
                ],
                [
                    'timestamp' => '15/10/2025 · 5:00pm',
                    'body' => 'Spoke to the customer and advised that the property has damp and will require extensive work.',
                ],
            ],
        ];

        return view('surveyor.surveys.detail_mock', compact('detail'));
    }

    /**
     * Mock desk study screen for UI build
     */
    public function deskStudyMock()
    {
        $deskStudy = [
            'address' => '123, Sample Street, Kent DA9 9ZT',
            'map' => [
                'image' => 'https://images.pexels.com/photos/439391/pexels-photo-439391.jpeg?auto=compress&cs=tinysrgb&w=800',
                'longitude' => '-0.3112',
                'latitude' => '51.4728',
            ],
            'flood_risks' => [
                [
                    'title' => 'Rivers and Seas',
                    'options' => ['Very Low', 'Low', 'Medium', 'High'],
                    'value' => 'Very Low',
                ],
                [
                    'title' => 'Surface Water',
                    'options' => ['Very Low', 'Low', 'Medium', 'High'],
                    'value' => 'Low',
                ],
                [
                    'title' => 'Reservoirs',
                    'options' => ['Y', 'N'],
                    'value' => 'Y',
                ],
                [
                    'title' => 'Ground Water',
                    'options' => ['Y', 'N'],
                    'value' => 'N',
                ],
            ],
            'council_tax' => [
                'label' => 'Council Tax',
                'options' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
                'value' => 'C',
            ],
            'epc_rating' => [
                'label' => 'EPC Rating',
                'options' => ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
                'value' => 'D',
            ],
            'soil' => [
                'label' => 'Soil Type',
                'value' => 'Soilscope 7',
                'risk' => 'High',
            ],
            'planning' => [
                'title' => 'Planning Matters',
                'items' => [
                    [
                        'label' => 'Listed Building Grade',
                        'options' => ['N/A', '1', '2', '2*'],
                        'value' => 'N/A',
                    ],
                    [
                        'label' => 'Conservation Area',
                        'options' => ['Y', 'N'],
                        'value' => 'Y',
                    ],
                    [
                        'label' => 'Article 4',
                        'options' => ['Y', 'N'],
                        'value' => 'N',
                    ],
                ],
            ],
        ];

        return view('surveyor.surveys.desk_study_mock', compact('deskStudy'));
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



