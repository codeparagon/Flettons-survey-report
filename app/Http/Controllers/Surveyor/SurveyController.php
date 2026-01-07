<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyNote;
use App\Models\SurveySectionDefinition;
use App\Services\SurveyDataService;
use App\Services\SurveyPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        // Use SurveyDataService to get grouped data
        $surveyDataService = app(\App\Services\SurveyDataService::class);
        
        // Use database data (set to false to use mock data for development)
        $useMockData = false;
        $categories = $surveyDataService->getGroupedSurveyData($survey, $useMockData);
        
        // Get accommodation configuration data from database
        $accommodationDataService = app(\App\Services\SurveyAccommodationDataService::class);
        $accommodationSections = $accommodationDataService->getAccommodationConfigurationData($survey, $useMockData);

        // Get options mapping for dynamic options from database
        $optionsMapping = $surveyDataService->getOptionsMapping();

        // Get content sections
        $contentSections = $this->getContentSectionsForSurvey($survey, $categories);

        return view('surveyor.surveys.mocks.data', compact('survey', 'categories', 'accommodationSections', 'optionsMapping', 'contentSections'));
    }

    /**
     * Get content sections for a survey, grouped by their link type.
     * 
     * @param Survey $survey
     * @param array $categories
     * @return array
     */
    protected function getContentSectionsForSurvey(Survey $survey, array $categories): array
    {
        $contentSections = [
            'standalone' => [],
            'by_category' => [],
            'by_subcategory' => [],
        ];

        // Get all active content sections
        $allContentSections = \App\Models\SurveyContentSection::active()
            ->ordered()
            ->with(['category', 'subcategory'])
            ->get();

        // Get survey level to determine which categories/subcategories are relevant
        $surveyLevel = $survey->level ?? null;
        $relevantCategoryIds = [];
        $relevantSubcategoryIds = [];

        // Extract category and subcategory IDs from the categories array
        foreach ($categories as $categoryName => $subCategories) {
            foreach ($subCategories as $subCategoryName => $sections) {
                // Try to find the actual category/subcategory from database
                $category = \App\Models\SurveyCategory::where('display_name', $categoryName)->first();
                $subcategory = \App\Models\SurveySubcategory::where('display_name', $subCategoryName)->first();
                
                if ($category) {
                    $relevantCategoryIds[] = $category->id;
                }
                if ($subcategory) {
                    $relevantSubcategoryIds[] = $subcategory->id;
                }
            }
        }

        foreach ($allContentSections as $contentSection) {
            if ($contentSection->subcategory_id) {
                // Subcategory-linked: add to by_subcategory if it matches
                if (in_array($contentSection->subcategory_id, $relevantSubcategoryIds)) {
                    $subcategory = $contentSection->subcategory;
                    $category = $subcategory->category ?? null;
                    if ($category) {
                        $categoryName = $category->display_name;
                        $subcategoryName = $subcategory->display_name;
                        if (!isset($contentSections['by_subcategory'][$categoryName])) {
                            $contentSections['by_subcategory'][$categoryName] = [];
                        }
                        if (!isset($contentSections['by_subcategory'][$categoryName][$subcategoryName])) {
                            $contentSections['by_subcategory'][$categoryName][$subcategoryName] = [];
                        }
                        $contentSections['by_subcategory'][$categoryName][$subcategoryName][] = $contentSection;
                    }
                }
            } elseif ($contentSection->category_id) {
                // Category-linked: add to by_category if it matches
                if (in_array($contentSection->category_id, $relevantCategoryIds)) {
                    $category = $contentSection->category;
                    $categoryName = $category->display_name;
                    if (!isset($contentSections['by_category'][$categoryName])) {
                        $contentSections['by_category'][$categoryName] = [];
                    }
                    $contentSections['by_category'][$categoryName][] = $contentSection;
                }
            } else {
                // Standalone: add to standalone array
                $contentSections['standalone'][] = $contentSection;
            }
        }

        return $contentSections;
    }

    /**
     * Update content section content.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param \App\Models\SurveyContentSection $contentSection
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateContentSection(Request $request, Survey $survey, \App\Models\SurveyContentSection $contentSection)
    {
        // Verify surveyor has access to this survey
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $contentSection->update([
            'content' => $validated['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content section updated successfully.',
            'content_section' => [
                'id' => $contentSection->id,
                'title' => $contentSection->title,
                'content' => $contentSection->content,
            ],
        ]);
    }

    /**
     * Generate PDF report for a survey.
     * 
     * @param Survey $survey
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generatePdfReport(Survey $survey, Request $request)
    {
        // Verify surveyor has access to this survey
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $pdfService = app(SurveyPdfService::class);
            $pdf = $pdfService->generatePdf($survey);
            
            // Generate filename
            $jobReference = $survey->job_reference ?? 'survey';
            $date = now()->format('Y-m-d');
            $filename = "survey-report-{$jobReference}-{$date}.pdf";
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF report', [
                'survey_id' => $survey->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Failed to generate PDF report. Please try again.');
        }
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
    /**
     * Clone a section item via AJAX - renders section-item.blade.php partial
     * 
     * @param Request $request
     * @param Survey $survey
     * @return \Illuminate\Http\JsonResponse
     */
    public function cloneSectionItem(Request $request, Survey $survey)
    {
        // Surveyor can only clone sections for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'source_section_id' => 'required|string',
                'source_section_definition_id' => 'required',
                'selected_section' => 'required|string',
                'category_name' => 'required|string',
                'sub_category_name' => 'required|string',
                'form_data' => 'required|array',
                'form_data.section' => 'nullable|string',
                'form_data.location' => 'nullable|string',
                'form_data.structure' => 'nullable|string',
                'form_data.material' => 'nullable|string',
                'form_data.defects' => 'nullable|array',
                'form_data.remaining_life' => 'nullable|string',
                'form_data.remainingLife' => 'nullable|string',
                'form_data.costs' => 'nullable|array',
                'form_data.notes' => 'nullable|string',
                'form_data.photos' => 'nullable|array',
                'form_data.condition_rating' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Clone validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $surveyDataService = app(\App\Services\SurveyDataService::class);
        
        // Get the section definition ID (this is the actual ID from survey_section_definitions table)
        // Convert to integer if it's a string
        $sectionDefinitionId = (int) $validated['source_section_definition_id'];
        
        // Verify the section definition exists
        $sectionDefinition = \App\Models\SurveySectionDefinition::find($sectionDefinitionId);
        if (!$sectionDefinition) {
            return response()->json(['error' => 'Section definition not found'], 404);
        }
        
        // Extract base name from source section name
        $sourceSectionName = $request->input('source_section_name', '');
        $baseName = $surveyDataService->extractBaseName($sourceSectionName);
        
        // Build new section name: "Base Name [Selected Section]"
        $newSectionName = $baseName . ' [' . $validated['selected_section'] . ']';
        
        // Generate new unique ID (using timestamp for mock data)
        $newSectionId = 'clone_' . time() . '_' . mt_rand(1000, 9999);
        
        // Get form data from request
        $formData = $validated['form_data'];
        
        // Build section data array matching transformAssessmentToViewFormat structure
        $sectionData = [
            'id' => $newSectionId,
            'section_id' => $sectionDefinitionId, // Use the actual section definition ID
            'name' => $newSectionName,
            'completion' => 0, // New cloned section starts at 0
            'total' => 10,
            'condition_rating' => $formData['condition_rating'] ?? 'ni',
            'selected_section' => $validated['selected_section'],
            'location' => $formData['location'] ?? '',
            'structure' => $formData['structure'] ?? '',
            'material' => $formData['material'] ?? '',
            'defects' => $formData['defects'] ?? [],
            'remaining_life' => $formData['remaining_life'] ?? '',
            'costs' => $formData['costs'] ?? [],
            'notes' => $formData['notes'] ?? '',
            'photos' => $formData['photos'] ?? [],
            'has_report' => false, // Cloned sections start without report
        ];

        // Get options mapping for dynamic options
        $optionsMapping = $surveyDataService->getOptionsMapping();

        // Render the section-item partial
        $html = view('surveyor.surveys.mocks.partials.section-item', [
            'section' => $sectionData,
            'categoryName' => $validated['category_name'],
            'subCategoryName' => $validated['sub_category_name'],
            'optionsMapping' => $optionsMapping,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'section_id' => $newSectionId,
            'section_name' => $newSectionName,
        ]);
    }

    /**
     * Clone an accommodation item via AJAX - renders accommodation-section-item.blade.php partial
     * 
     * @param Request $request
     * @param Survey $survey
     * @return \Illuminate\Http\JsonResponse
     */
    public function cloneAccommodationItem(Request $request, Survey $survey)
    {
        // Surveyor can only clone accommodations for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'source_accommodation_id' => 'required|string',
                'accommodation_type_id' => 'required|integer|exists:survey_accommodation_types,id',
                'form_data' => 'required|array',
                'form_data.custom_name' => 'nullable|string',
                'form_data.notes' => 'nullable|string',
                'form_data.components' => 'nullable|array',
                'form_data.components.*.component_key' => 'nullable|string',
                'form_data.components.*.material' => 'nullable|string',
                'form_data.components.*.defects' => 'nullable|array',
                'form_data.condition_rating' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Accommodation clone validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $accommodationDataService = app(\App\Services\SurveyAccommodationDataService::class);
        $accommodationTypeId = $validated['accommodation_type_id'];
        $formData = $validated['form_data'];
        
        // Get accommodation type
        $accommodationType = \App\Models\SurveyAccommodationType::findOrFail($accommodationTypeId);
        
        // Count existing accommodations of this type to determine next number
        $existingCount = \App\Models\SurveyAccommodationAssessment::where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->count();
        $nextNumber = $existingCount + 1;
        
        // Generate new unique ID for the clone
        $newAccommodationId = 'clone_' . time() . '_' . mt_rand(1000, 9999);
        
        // Get components from form data or use default
        $components = $formData['components'] ?? [];
        if (empty($components)) {
            // Get default components for this accommodation type
            $defaultComponents = $accommodationDataService->getAccommodationComponents();
            $components = array_map(function($component) {
                return [
                    'component_key' => $component['key'],
                    'component_name' => $component['name'],
                    'material' => '',
                    'defects' => [],
                ];
            }, $defaultComponents);
        } else {
            // Ensure component_name is populated for each component
            $allComponents = $accommodationDataService->getAccommodationComponents();
            $componentNames = collect($allComponents)->pluck('name', 'key')->toArray();
            
            $components = array_map(function($component) use ($componentNames) {
                // Add component_name if not present
                if (!isset($component['component_name']) && isset($component['component_key'])) {
                    $component['component_name'] = $componentNames[$component['component_key']] ?? ucfirst(str_replace('_', ' ', $component['component_key']));
                }
                return $component;
            }, $components);
        }
        
        // Determine accommodation name - use form data if provided, otherwise increment
        $accommodationName = $formData['custom_name'] ?? null;
        if (!$accommodationName) {
            // Extract base name from source or use accommodation type name
            $baseName = $accommodationType->display_name;
            $accommodationName = $baseName . ' ' . $nextNumber;
        }
        
        // Build accommodation data array
        $accommodationData = [
            'id' => $newAccommodationId,
            'name' => $accommodationName,
            'accommodation_type_id' => $accommodationTypeId,
            'accommodation_type_name' => $accommodationType->display_name,
            'condition_rating' => $formData['condition_rating'] ?? 'ni',
            'notes' => $formData['notes'] ?? '',
            'photos' => [],
            'components' => $components,
        ];

        // Render the accommodation-section-item partial
        $html = view('surveyor.surveys.mocks.partials.accommodation-section-item', [
            'accommodation' => $accommodationData,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'accommodation_id' => $newAccommodationId,
            'accommodation_name' => $accommodationData['name'],
        ]);
    }

    /**
     * Save section assessment and generate report.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $sectionDefinitionId Section definition ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSectionAssessment(Request $request, Survey $survey, int $sectionDefinitionId)
    {
        // Surveyor can only save assessments for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Validate request
            $validated = $request->validate([
                'section' => 'nullable|string',
                'location' => 'nullable|string',
                'structure' => 'nullable|string',
                'material' => 'nullable|string',
                'defects' => 'nullable|array',
                'remaining_life' => 'nullable|string',
                'notes' => 'nullable|string',
                'costs' => 'nullable|array',
                'costs.*.category' => 'required_with:costs|string',
                'costs.*.description' => 'required_with:costs|string',
                'costs.*.due' => 'nullable|string',
                'costs.*.cost' => 'nullable|string',
                'condition_rating' => 'nullable|string',
                'section_id' => 'nullable|string', // Frontend section ID to detect clones
                'photos' => 'nullable|array',
                'photos.*' => 'nullable|image|max:10240', // Max 10MB per image
            ]);

            // Get section definition
            $sectionDefinition = SurveySectionDefinition::findOrFail($sectionDefinitionId);

            // Get service
            $service = app(SurveyDataService::class);

            // Determine if this is a clone and get the assessment ID if it exists
            $sectionId = $validated['section_id'] ?? null;
            $assessmentId = null;
            $isClone = false;
            
            if ($sectionId) {
                // Check if section_id is a numeric ID (already saved assessment)
                if (is_numeric($sectionId)) {
                    $assessmentId = (int) $sectionId;
                    // Check if this assessment exists and is a clone
                    $existingAssessment = \App\Models\SurveySectionAssessment::find($assessmentId);
                    if ($existingAssessment) {
                        $isClone = $existingAssessment->is_clone ?? false;
                    }
                } elseif (strpos($sectionId, 'clone_') === 0) {
                    // New clone that hasn't been saved yet
                    $isClone = true;
                }
            }

            // Log condition_rating before saving
            Log::info('Condition rating received in controller', [
                'condition_rating' => $validated['condition_rating'] ?? null,
                'raw_request' => $request->input('condition_rating'),
            ]);
            
            // Save assessment and generate report
            $result = $service->saveSectionAssessment($survey, $sectionDefinition, $validated, $isClone, $assessmentId);
            
            // Save photos if provided
            if ($request->hasFile('photos')) {
                $service->saveSectionPhotos($result['assessment'], $request->file('photos'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Assessment saved successfully',
                'assessment_id' => $result['assessment']->id,
                'report_content' => $result['report_content'],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to save section assessment', [
                'survey_id' => $survey->id,
                'section_definition_id' => $sectionDefinitionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to save assessment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save accommodation assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAccommodationAssessment(Request $request, Survey $survey)
    {
        // Surveyor can only save assessments for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Validate request
            $validated = $request->validate([
                'accommodation_id' => 'nullable|string', // Frontend accommodation ID to detect clones
                'accommodation_type_id' => 'required|integer|exists:survey_accommodation_types,id',
                'custom_name' => 'nullable|string|max:150',
                'components' => 'nullable|array', // Made optional - form can submit even if components are not filled
                'components.*.component_key' => 'nullable|string', // Allow missing or empty component_key - components can be incomplete
                'components.*.material' => 'nullable|string',
                'components.*.defects' => 'nullable|array',
                'components.*.defects.*' => 'nullable|string',
                'notes' => 'nullable|string',
                'condition_rating' => 'nullable|string|in:1,2,3,ni',
                'photos' => 'nullable|array',
                'photos.*' => 'nullable|image|max:10240', // Max 10MB per image
            ]);

            // Get accommodation type
            $accommodationTypeId = $validated['accommodation_type_id'];

            // Get service
            $service = app(\App\Services\SurveyAccommodationDataService::class);

            // Determine if this is a clone and get the assessment ID if it exists
            $accommodationId = $validated['accommodation_id'] ?? null;
            $assessmentId = null;
            $isClone = false;
            
            if ($accommodationId) {
                // Check if accommodation_id is a numeric ID (already saved assessment)
                if (is_numeric($accommodationId)) {
                    $assessmentId = (int) $accommodationId;
                    // Check if this assessment exists and is a clone
                    $existingAssessment = \App\Models\SurveyAccommodationAssessment::find($assessmentId);
                    if ($existingAssessment) {
                        $isClone = $existingAssessment->clone_index > 0;
                    }
                } elseif (strpos($accommodationId, 'clone_') === 0) {
                    // New clone - extract source assessment ID
                    $isClone = true;
                    $sourceIdStr = substr($accommodationId, 6); // Remove 'clone_' prefix
                    if (is_numeric($sourceIdStr)) {
                        $assessmentId = (int) $sourceIdStr; // Use source ID for cloning
                    }
                }
            }
            
            // Save assessment and generate report
            $result = $service->saveAccommodationAssessment($survey, $accommodationTypeId, $validated, $isClone, $assessmentId);
            
            // Save photos if provided
            if ($request->hasFile('photos')) {
                $service->saveAccommodationPhotos($result['assessment'], $request->file('photos'));
            }

            $message = $isClone 
                ? 'Accommodation cloned successfully' 
                : 'Accommodation assessment saved successfully';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'assessment_id' => $result['assessment']->id,
                'report_content' => $result['report_content'],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to save accommodation assessment', [
                'survey_id' => $survey->id,
                'accommodation_type_id' => $request->input('accommodation_type_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to save accommodation assessment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update condition rating for an existing assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $assessmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateConditionRating(Request $request, Survey $survey, int $assessmentId)
    {
        // Surveyor can only update assessments for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Validate request
            $validated = $request->validate([
                'condition_rating' => 'required|string',
            ]);

            // Map condition rating using the service
            $service = app(SurveyDataService::class);
            $mappedRating = $service->mapConditionRating($validated['condition_rating']);

            // Try to find as section assessment first
            $assessment = \App\Models\SurveySectionAssessment::where('id', $assessmentId)
                ->where('survey_id', $survey->id)
                ->first();

            // If not found, try accommodation assessment
            if (!$assessment) {
                $assessment = \App\Models\SurveyAccommodationAssessment::where('id', $assessmentId)
                    ->where('survey_id', $survey->id)
                    ->first();
            }

            // If still not found, return error
            if (!$assessment) {
                Log::warning('Assessment not found for condition rating update', [
                    'survey_id' => $survey->id,
                    'assessment_id' => $assessmentId,
                ]);
                return response()->json([
                    'error' => 'Assessment not found',
                ], 404);
            }

            // Log before update
            $assessmentType = $assessment instanceof \App\Models\SurveySectionAssessment ? 'section' : 'accommodation';
            Log::info('Updating condition rating', [
                'assessment_id' => $assessment->id,
                'assessment_type' => $assessmentType,
                'current_rating' => $assessment->condition_rating,
                'new_rating' => $mappedRating,
                'raw_value' => $validated['condition_rating'],
            ]);

            // Update condition rating using update() method to ensure it's saved
            // update() returns the number of affected rows (should be 1)
            $updatedRows = $assessment->update(['condition_rating' => $mappedRating]);
            
            if ($updatedRows === 0) {
                Log::error('Failed to update condition rating - no rows affected', [
                    'assessment_id' => $assessment->id,
                    'assessment_type' => $assessmentType,
                    'mapped_rating' => $mappedRating,
                ]);
                return response()->json([
                    'error' => 'Failed to update condition rating - no rows were updated',
                ], 500);
            }

            // Refresh to get updated value
            $assessment->refresh();

            Log::info('Condition rating updated successfully', [
                'assessment_id' => $assessment->id,
                'assessment_type' => $assessmentType,
                'raw_value' => $validated['condition_rating'],
                'mapped_value' => $mappedRating,
                'saved_value' => $assessment->condition_rating,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Condition rating updated successfully',
                'condition_rating' => $mappedRating,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to update condition rating', [
                'survey_id' => $survey->id,
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to update condition rating: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update costs for an existing assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $assessmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCosts(Request $request, Survey $survey, int $assessmentId)
    {
        // Surveyor can only update assessments for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Validate request
            $validated = $request->validate([
                'costs' => 'required|array',
                'costs.*.category' => 'required|string',
                'costs.*.description' => 'required|string',
                'costs.*.due' => 'nullable|string',
                'costs.*.cost' => 'nullable|string',
            ]);

            // Find the assessment
            $assessment = \App\Models\SurveySectionAssessment::where('id', $assessmentId)
                ->where('survey_id', $survey->id)
                ->firstOrFail();

            // Get service
            $service = app(SurveyDataService::class);
            
            // Save costs using the service
            $service->saveSectionCosts($assessment, $validated['costs']);

            Log::info('Costs updated', [
                'assessment_id' => $assessment->id,
                'costs_count' => count($validated['costs']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Costs updated successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Assessment not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update costs', [
                'survey_id' => $survey->id,
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to update costs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload photos for an existing assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $assessmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhotos(Request $request, Survey $survey, int $assessmentId)
    {
        // Surveyor can only update assessments for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Validate request
            $validated = $request->validate([
                'photos' => 'required|array',
                'photos.*' => 'required|image|max:10240', // Max 10MB per image
            ]);

            // Find the assessment
            $assessment = \App\Models\SurveySectionAssessment::where('id', $assessmentId)
                ->where('survey_id', $survey->id)
                ->firstOrFail();

            // Get service
            $service = app(SurveyDataService::class);
            
            // Save photos
            $service->saveSectionPhotos($assessment, $request->file('photos'));

            Log::info('Photos uploaded', [
                'assessment_id' => $assessment->id,
                'photos_count' => count($request->file('photos')),
            ]);

            // Reload photos to return them
            $assessment->load('photos');
            $photos = $assessment->photos->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => asset('storage/' . ltrim($photo->file_path, '/')),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Photos uploaded successfully',
                'photos' => $photos,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Assessment not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to upload photos', [
                'survey_id' => $survey->id,
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to upload photos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a photo from an assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $assessmentId
     * @param int $photoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePhoto(Request $request, Survey $survey, int $assessmentId, int $photoId)
    {
        // Surveyor can only delete photos for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Find the assessment
            $assessment = \App\Models\SurveySectionAssessment::where('id', $assessmentId)
                ->where('survey_id', $survey->id)
                ->firstOrFail();

            // Find the photo
            $photo = \App\Models\SurveySectionPhoto::where('id', $photoId)
                ->where('section_assessment_id', $assessment->id)
                ->firstOrFail();

            // Delete file from storage
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }

            // Delete photo record
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Failed to delete photo', [
                'survey_id' => $survey->id,
                'assessment_id' => $assessmentId,
                'photo_id' => $photoId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to delete photo: ' . $e->getMessage(),
            ], 500);
        }
    }

}
