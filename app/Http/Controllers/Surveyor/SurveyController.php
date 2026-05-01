<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyDeskStudy;
use App\Models\SurveyAccommodationType;
use App\Models\SurveyNote;
use App\Models\SurveySectionDefinition;
use App\Services\SurveyAccommodationDataService;
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

        $accService = app(SurveyAccommodationDataService::class);
        $levelOptionValues = ['Level 1', 'Level 2', 'Level 3', 'Specialist'];
        $propertyCountTypesByLevel = [];
        foreach ($levelOptionValues as $levelKey) {
            $propertyCountTypesByLevel[$levelKey] = $accService->getPropertyCountTypesForLevel($levelKey)
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'display_name' => $t->display_name,
                    'sort_order' => $t->sort_order,
                ])
                ->values()
                ->all();
        }

        return view('surveyor.surveys.index', compact(
            'assignedSurveys',
            'unassignedSurveys',
            'propertyCountTypesByLevel'
        ));
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

        $propertyCountTypesForSurvey = $this->buildPropertyCountTypesForSurveyView($survey);

        return view('surveyor.surveys.mocks.detail', compact('survey', 'propertyCountTypesForSurvey'));
    }

    public function surveyDetails($id)
    {
        $survey = Survey::findOrFail($id);
        $propertyCountTypesForSurvey = $this->buildPropertyCountTypesForSurveyView($survey);

        return view('surveyor.surveys.mocks.detail', compact('survey', 'propertyCountTypesForSurvey'));
    }

    /**
     * @return list<array{id: int, display_name: string, count: int}>
     */
    protected function buildPropertyCountTypesForSurveyView(Survey $survey): array
    {
        $acc = app(SurveyAccommodationDataService::class);
        $types = $acc->getPropertyCountTypesForLevel($survey->level ?? '');
        $resolved = $acc->getResolvedPropertyAccommodationCounts($survey);

        return $types->map(function ($t) use ($resolved) {
            return [
                'id' => $t->id,
                'display_name' => $t->display_name,
                'count' => $resolved[$t->id] ?? 0,
            ];
        })->values()->all();
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

        $defaultsFlood = [
            ['label' => 'Rivers and Seas', 'value' => 'Very Low'],
            ['label' => 'Surface Water', 'value' => 'Low'],
            ['label' => 'Reservoirs', 'value' => 'Yes'],
            ['label' => 'Ground Water', 'value' => 'No'],
        ];

        $defaultsPlanning = [
            ['label' => 'Council Tax', 'value' => 'Band C'],
            ['label' => 'EPC Rating', 'value' => 'D'],
            ['label' => 'Soil Type', 'value' => 'Soilscope 7 (High Risk)'],
            ['label' => 'Listed Building', 'value' => $survey->listed_building ?? 'N/A'],
            ['label' => 'Conservation Area', 'value' => 'Yes'],
            ['label' => 'Article 4', 'value' => 'No'],
        ];

        $desk = SurveyDeskStudy::firstOrCreate(
            ['survey_id' => $survey->id],
            [
                'address' => $survey->full_address ?? '123, Sample Street, Kent DA9 9ZT',
                'job_reference' => $survey->job_reference ?? '12SE39DT-SH',
                'longitude' => '-0.3112',
                'latitude' => '51.4728',
                'map_image_path' => null,
                'flood_risks' => $defaultsFlood,
                'planning' => $defaultsPlanning,
            ]
        );

        $mapImageUrl = null;
        if (!empty($desk->map_image_path)) {
            try {
                $mapImageUrl = Storage::disk('public')->url($desk->map_image_path);
            } catch (\Throwable $e) {
                $mapImageUrl = null;
            }
        }

        $deskStudy = [
            'address' => $desk->address,
            'job_reference' => $desk->job_reference,
            'map' => [
                'image_url' => $mapImageUrl,
                'longitude' => $desk->longitude,
                'latitude' => $desk->latitude,
            ],
            'flood_risks' => $desk->flood_risks ?: $defaultsFlood,
            'planning' => $desk->planning ?: $defaultsPlanning,
            'updated_at' => optional($desk->updated_at)->toIso8601String(),
        ];

        return view('surveyor.surveys.mocks.desk_study', compact('survey', 'deskStudy'));
    }

    public function saveDeskStudyMock(Request $request, Survey $survey)
    {
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'job_reference' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'flood_risks' => ['nullable', 'array'],
            'flood_risks.*.label' => ['required_with:flood_risks', 'string', 'max:255'],
            'flood_risks.*.value' => ['required_with:flood_risks', 'string', 'max:255'],
            'planning' => ['nullable', 'array'],
            'planning.*.label' => ['required_with:planning', 'string', 'max:255'],
            'planning.*.value' => ['required_with:planning', 'string', 'max:255'],
        ]);

        $desk = SurveyDeskStudy::firstOrCreate(['survey_id' => $survey->id]);
        $desk->fill([
            'address' => $validated['address'] ?? null,
            'job_reference' => $validated['job_reference'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'flood_risks' => $validated['flood_risks'] ?? [],
            'planning' => $validated['planning'] ?? [],
        ]);
        $desk->save();

        return response()->json([
            'success' => true,
            'message' => 'Desk study saved',
            'updated_at' => optional($desk->updated_at)->toIso8601String(),
        ]);
    }

    public function uploadDeskStudyMockMapImage(Request $request, Survey $survey)
    {
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'map_image' => ['required', 'file', 'image', 'max:5120'], // 5MB
        ]);

        $desk = SurveyDeskStudy::firstOrCreate(['survey_id' => $survey->id]);

        // delete old
        if (!empty($desk->map_image_path)) {
            try {
                Storage::disk('public')->delete($desk->map_image_path);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $file = $validated['map_image'];
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $path = $file->storeAs('desk-studies/' . $survey->id, 'map.' . $ext, 'public');

        $desk->map_image_path = $path;
        $desk->save();

        return response()->json([
            'success' => true,
            'message' => 'Map image uploaded',
            'map_image_url' => Storage::disk('public')->url($path),
            'updated_at' => optional($desk->updated_at)->toIso8601String(),
        ]);
    }

    public function deleteDeskStudyMockMapImage(Request $request, Survey $survey)
    {
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $desk = SurveyDeskStudy::firstOrCreate(['survey_id' => $survey->id]);
        if (!empty($desk->map_image_path)) {
            try {
                Storage::disk('public')->delete($desk->map_image_path);
            } catch (\Throwable $e) {
                // ignore
            }
        }
        $desk->map_image_path = null;
        $desk->save();

        return response()->json([
            'success' => true,
            'message' => 'Map image removed',
            'map_image_url' => null,
            'updated_at' => optional($desk->updated_at)->toIso8601String(),
        ]);
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
        $accommodationLocationOptions = $accommodationDataService->getGlobalLocations();

        // Get accommodation types with components (for form dropdowns)
        // Only types that have components configured will be available
        $accommodationTypesWithComponents = $accommodationDataService->getAccommodationTypesWithComponents();
        
        // Check if we should show the accommodation section (even if no assessments yet)
        $hasAccommodationTypesWithComponents = count($accommodationTypesWithComponents) > 0;

        // Get options mapping for dynamic options from database
        $optionsMapping = $surveyDataService->getOptionsMapping();

        // Load condition rating rules (admin-configured mapping)
        // JS matching is case-insensitive via normalized (trimmed, lowercased) keys.
        $conditionRatingRules = [
            'material' => [],
            'defects' => [],
        ];

        $ruleRows = \App\Models\SurveyConditionRatingRule::query()
            ->whereNotNull('condition_rating')
            ->get(['option_type', 'option_value', 'condition_rating']);

        foreach ($ruleRows as $row) {
            $type = $row->option_type;
            $conditionRatingRules[$type][(string) $row->option_value] = (int) $row->condition_rating;
        }

        // Get content sections
        $contentSections = $this->getContentSectionsForSurvey($survey, $categories);

        return view('surveyor.surveys.mocks.data', compact(
            'survey',
            'categories',
            'accommodationSections',
            'accommodationLocationOptions',
            'accommodationTypesWithComponents',
            'hasAccommodationTypesWithComponents',
            'optionsMapping',
            'conditionRatingRules',
            'contentSections'
        ));
    }

    /**
     * Find SurveyLevel by matching survey level value.
     * Handles formats like "Level 1", "level_1", "Level 1 - Condition Report", etc.
     */
    protected function findSurveyLevelByValue($levelValue)
    {
        if (empty($levelValue)) {
            return null;
        }
        
        // Try exact match on name first
        $level = \App\Models\SurveyLevel::where('name', $levelValue)->first();
        if ($level) {
            return $level;
        }
        
        // Try exact match on display_name
        $level = \App\Models\SurveyLevel::where('display_name', $levelValue)->first();
        if ($level) {
            return $level;
        }
        
        // Try to extract level number and match (e.g., "Level 1" -> "level_1")
        // Extract number from "Level 1", "level_1", "Level 1 - Condition Report", etc.
        if (preg_match('/level[_\s]*(\d+)/i', $levelValue, $matches)) {
            $levelNumber = $matches[1];
            $normalizedName = 'level_' . $levelNumber;
            $level = \App\Models\SurveyLevel::where('name', $normalizedName)->first();
            if ($level) {
                return $level;
            }
        }
        
        return null;
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

        // Get content sections based on survey level
        // If survey has no level set (null/empty), show all sections for backward compatibility
        // If survey has a level set, only show sections assigned to that level
        
        if (empty($survey->level)) {
            // No level set - show all active content sections (backward compatibility for old surveys)
            $allContentSections = \App\Models\SurveyContentSection::active()
                ->ordered()
                ->with(['category', 'subcategory'])
                ->get();
        } else {
            // Level is set - only show sections assigned to this level
            $surveyLevel = $this->findSurveyLevelByValue($survey->level);
            
            if (!$surveyLevel) {
                // Level doesn't exist in database - return empty
                $allContentSections = collect();
            } else {
                // Level exists - get assigned content sections
                $contentSectionIds = $surveyLevel->contentSections()->pluck('survey_content_sections.id')->unique();
                
                if ($contentSectionIds->isEmpty()) {
                    // Level exists but has no content sections assigned - return empty
                    $allContentSections = collect();
                } else {
                    // Level exists and has content sections - return only those sections
                    $allContentSections = \App\Models\SurveyContentSection::whereIn('id', $contentSectionIds)
                        ->active()
                        ->ordered()
                        ->with(['category', 'subcategory'])
                        ->get();
                }
            }
        }

        // Get survey level name to determine which categories/subcategories are relevant
        $surveyLevelName = $survey->level ?? null;
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
        $accService = app(SurveyAccommodationDataService::class);
        $allowedTypes = $accService->getPropertyCountTypesForLevel($request->input('level'));
        $allowedIds = $allowedTypes->pluck('id')->all();

        $validated = $request->validate([
            'level' => 'required|string|max:100',
            'scheduled_date' => 'nullable|date',
            'full_address' => 'nullable|string|max:500',
            'postcode' => 'nullable|string|max:20',
            'job_reference' => 'nullable|string|max:100',
            'house_or_flat' => 'nullable|string|max:50',
            'listed_building' => 'nullable|string|max:50',
            'property_accommodation_counts' => 'nullable|array',
            'property_accommodation_counts.*' => 'nullable|integer|min:0|max:100',
        ]);

        $rawCounts = $request->input('property_accommodation_counts', []);
        $normalized = [];
        foreach ($rawCounts as $key => $val) {
            $id = (int) $key;
            if (! in_array($id, $allowedIds, true)) {
                continue;
            }
            if ($val === null || $val === '') {
                continue;
            }
            $normalized[(string) $id] = max(0, min(100, (int) $val));
        }

        foreach ($allowedIds as $aid) {
            $sk = (string) $aid;
            if (! array_key_exists($sk, $normalized)) {
                $normalized[$sk] = 0;
            }
        }

        $survey = Survey::create([
            'surveyor_id' => auth()->id(),
            'level' => $validated['level'],
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'full_address' => $validated['full_address'] ?? null,
            'postcode' => $validated['postcode'] ?? null,
            'job_reference' => $validated['job_reference'] ?? null,
            'house_or_flat' => $validated['house_or_flat'] ?? null,
            'listed_building' => $validated['listed_building'] ?? null,
            'property_accommodation_counts' => $normalized === [] ? null : $normalized,
        ]);

        if ($normalized !== []) {
            $accService->syncLegacyPropertyCountColumns($survey, $normalized);
        }

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
            if (! $survey) {
                return response()->json(['status' => 'error', 'message' => 'Survey not found'], 404);
            }

            if ($request->field === 'property_accommodation_count') {
                $request->validate([
                    'accommodation_type_id' => 'required|integer|exists:survey_accommodation_types,id',
                    'value' => 'nullable|string|max:10',
                ]);

                $accService = app(SurveyAccommodationDataService::class);
                $typeId = (int) $request->accommodation_type_id;
                $allowedIds = $accService->getPropertyCountTypesForLevel($survey->level ?? '')->pluck('id')->all();

                if (! in_array($typeId, $allowedIds, true)) {
                    return response()->json(['status' => 'error', 'message' => 'Invalid type for this survey level'], 422);
                }

                $type = SurveyAccommodationType::find($typeId);
                if (! $type || ! $type->counts_toward_property) {
                    return response()->json(['status' => 'error', 'message' => 'Type is not used for property counts'], 422);
                }

                $rawVal = $request->value;
                $num = 0;
                if ($rawVal !== null && $rawVal !== '' && $rawVal !== '-') {
                    $num = max(0, min(100, (int) $rawVal));
                }

                $counts = $survey->property_accommodation_counts;
                if (! is_array($counts)) {
                    $counts = [];
                }
                $counts[(string) $typeId] = $num;
                $survey->property_accommodation_counts = $counts;
                $survey->save();
                $accService->syncLegacyPropertyCountColumns($survey, [$typeId => $num]);

                // If the admin has configured this accommodation type to have components,
                // increasing the count should create Bedroom 2, Bedroom 3, ... rows for the data page.
                // Non-destructive on decreases.
                try {
                    $accService->ensureAccommodationAssessmentsForTypeCount($survey, $typeId, $num);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to ensure accommodation assessments for property count update', [
                        'survey_id' => $survey->id,
                        'accommodation_type_id' => $typeId,
                        'desired_count' => $num,
                        'error' => $e->getMessage(),
                    ]);
                }

                return response()->json(['status' => 'success', 'message' => 'Survey updated successfully.', 'survey' => $survey]);
            }

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
                'form_data.options' => 'nullable|array',
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
        $sectionDefinition = \App\Models\SurveySectionDefinition::with('subcategory')->find($sectionDefinitionId);
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
        $normalizedOpts = $surveyDataService->normalizeOptionsFromFormData($formData);

        // Build section data array matching transformAssessmentToViewFormat structure
        $sectionData = [
            'id' => $newSectionId,
            'section_id' => $sectionDefinitionId, // Use the actual section definition ID
            'name' => $newSectionName,
            'subcategory_key' => $sectionDefinition->subcategory->name ?? '',
            'completion' => 0, // New cloned section starts at 0
            'total' => 10,
            'condition_rating' => $formData['condition_rating'] ?? 'ni',
            'selected_section' => $validated['selected_section'],
            'location' => $normalizedOpts['location'] ?? ($formData['location'] ?? ''),
            'structure' => $normalizedOpts['structure'] ?? ($formData['structure'] ?? ''),
            'material' => $normalizedOpts['material'] ?? ($formData['material'] ?? ''),
            'defects' => isset($normalizedOpts['defects']) ? (array) $normalizedOpts['defects'] : ($formData['defects'] ?? []),
            'remaining_life' => $normalizedOpts['remaining_life'] ?? ($formData['remaining_life'] ?? ''),
            'enabled_option_fields' => $surveyDataService->buildEnabledOptionFieldsMeta(
                $surveyDataService->getEnabledOptionTypesForSection($sectionDefinition)
            ),
            'option_selections' => $normalizedOpts,
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
                'form_data.components.*.location' => 'nullable|string|max:255',
                'form_data.components.*.defects' => 'nullable|array',
                'form_data.condition_rating' => 'nullable|string',
                'form_data.location' => 'nullable|string|max:255',
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
        
        // Get accommodation type and verify it has components configured
        $accommodationType = \App\Models\SurveyAccommodationType::with('components')->findOrFail($accommodationTypeId);
        if (!$accommodationType->components || $accommodationType->components->count() === 0) {
            return response()->json([
                'error' => 'This accommodation type does not have any components configured. Please configure components in the admin panel first.',
            ], 422);
        }
        
        // Count existing accommodations of this type to determine next number
        $existingCount = \App\Models\SurveyAccommodationAssessment::where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->count();
        $nextNumber = $existingCount + 1;
        
        // Temporary client ID until first save — must embed numeric source so save can resolve clone_src123_…
        $sourceRaw = $validated['source_accommodation_id'] ?? '';
        if (is_numeric($sourceRaw)) {
            $newAccommodationId = 'clone_src' . (int) $sourceRaw . '_' . time() . '_' . mt_rand(1000, 9999);
        } else {
            $newAccommodationId = 'clone_' . time() . '_' . mt_rand(1000, 9999);
        }
        
        // Get components from form data or use components linked to this accommodation type
        $components = $formData['components'] ?? [];
        if (empty($components)) {
            // Get components linked to this accommodation type (not all components)
            $accommodationType->load('components');
            if ($accommodationType->components && $accommodationType->components->count() > 0) {
                $components = $accommodationType->components->map(function ($component) {
                    return [
                        'component_id' => $component->id,
                        'component_key' => $component->key_name,
                        'component_name' => $component->display_name,
                        'location' => '',
                        'material' => '',
                        'defects' => [],
                    ];
                })->toArray();
            } else {
                // If no components linked, return empty array
                $components = [];
            }
        } else {
            // Ensure component_name and component_id are populated for each component
            $accommodationType->load('components');
            $componentMap = $accommodationType->components->keyBy('key_name');

            $components = array_map(function ($component) use ($componentMap) {
                if (!isset($component['component_name']) && isset($component['component_key'])) {
                    $typeComponent = $componentMap->get($component['component_key']);
                    $component['component_name'] = $typeComponent ? $typeComponent->display_name : ucfirst(str_replace('_', ' ', $component['component_key']));
                    if ($typeComponent) {
                        $component['component_id'] = $typeComponent->id;
                    }
                } elseif (isset($component['component_key'])) {
                    $typeComponent = $componentMap->get($component['component_key']);
                    if ($typeComponent && empty($component['component_id'])) {
                        $component['component_id'] = $typeComponent->id;
                    }
                }

                return $component;
            }, $components);
        }

        // Always number by server-side count. The client sends the source row's custom_name (e.g. "Bedroom 1")
        // for every clone, which would otherwise label every new row as "Bedroom 1".
        $hasMultiple = $nextNumber > 1;
        $displayLabel = $hasMultiple
            ? ($accommodationType->display_name . ' ' . $nextNumber)
            : $accommodationType->display_name;
        $accommodationName = $displayLabel;

        $completedComponents = 0;
        foreach ($components as $c) {
            $mat = $c['material'] ?? '';
            $def = $c['defects'] ?? [];
            $hasDef = is_array($def) && count(array_filter($def)) > 0;
            if (($mat !== '' && $mat !== null) || $hasDef) {
                $completedComponents++;
            }
        }

        // Build accommodation data array (display_label matches Bedroom 1 / Bedroom 2 pattern)
        $accommodationData = [
            'id' => $newAccommodationId,
            'name' => $displayLabel,
            'display_label' => $displayLabel,
            'clone_index' => max(0, $nextNumber - 1),
            'accommodation_type_id' => $accommodationTypeId,
            'accommodation_type_name' => $accommodationType->display_name,
            'condition_rating' => $formData['condition_rating'] ?? 'ni',
            'notes' => $formData['notes'] ?? '',
            'location' => $formData['location'] ?? '',
            'photos' => [],
            'report_content' => '',
            'has_report' => false,
            'form_submitted' => false,
            'completed_components' => $completedComponents,
            'total_components' => $accommodationType->components->count(),
            'components' => $components,
        ];

        // Render the accommodation-section-item partial
        $html = view('surveyor.surveys.mocks.partials.accommodation-section-item', [
            'accommodation' => $accommodationData,
            'accommodationLocationOptions' => $accommodationDataService->getGlobalLocations(),
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
                // Location may be a single string or an array (multi-select UI). If array, elements must be strings.
                'location' => 'nullable',
                'location.*' => 'nullable|string',
                'structure' => 'nullable|string',
                'material' => 'nullable|string',
                'defects' => 'nullable|array',
                'remaining_life' => 'nullable|string',
                'options' => 'nullable|array',
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

            $validated['options'] = $request->input('options', []);

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

            // Load photos for response so frontend can show them without refresh
            $result['assessment']->load('photos');
            $photos = $result['assessment']->photos->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => Storage::disk('public')->url($photo->file_path),
                ];
            })->values()->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Assessment saved successfully',
                'assessment_id' => $result['assessment']->id,
                'report_content' => $result['report_content'],
                'photos' => $photos,
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
                'components.*.location' => 'nullable|string|max:255',
                'components.*.material' => 'nullable|string',
                'components.*.defects' => 'nullable|array',
                'components.*.defects.*' => 'nullable|string',
                'notes' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'condition_rating' => 'nullable|string|in:1,2,3,ni',
                'photos' => 'nullable|array',
                'photos.*' => 'nullable|image|max:10240', // Max 10MB per image
            ]);

            // Get accommodation type
            $accommodationTypeId = $validated['accommodation_type_id'];
            
            // Verify that the accommodation type has components configured
            $accommodationType = \App\Models\SurveyAccommodationType::with('components')->findOrFail($accommodationTypeId);
            if (!$accommodationType->components || $accommodationType->components->count() === 0) {
                return response()->json([
                    'error' => 'This accommodation type does not have any components configured. Please configure components in the admin panel first.',
                ], 422);
            }

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
                    // Pending clone: extract source assessment ID when embedded (clone_src123_…)
                    $isClone = true;
                    if (preg_match('/^clone_src(\d+)_/', $accommodationId, $m)) {
                        $assessmentId = (int) $m[1];
                    } elseif (preg_match('/^clone_(\d+)$/', $accommodationId, $m)) {
                        $assessmentId = (int) $m[1];
                    }
                }
            }
            
            // Save assessment and generate report
            $result = $service->saveAccommodationAssessment($survey, $accommodationTypeId, $validated, $isClone, $assessmentId);
            
            // Save photos if provided
            if ($request->hasFile('photos')) {
                $service->saveAccommodationPhotos($result['assessment'], $request->file('photos'));
            }

            // Load photos for response so frontend can show them without refresh
            $result['assessment']->load('photos');
            $photos = $result['assessment']->photos->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => Storage::disk('public')->url($photo->file_path),
                ];
            })->values()->toArray();

            $message = $isClone 
                ? 'Accommodation cloned successfully' 
                : 'Accommodation assessment saved successfully';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'assessment_id' => $result['assessment']->id,
                'report_content' => $result['report_content'],
                'report_generation_error' => $result['report_generation_error'] ?? null,
                // Combined narratives removed
                'photos' => $photos,
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

    // Combined narratives removed (component summary generate/save endpoints)

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

            // Reload photos to return them (use storage URL for absolute URLs on production)
            $assessment->load('photos');
            $photos = $assessment->photos->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => Storage::disk('public')->url($photo->file_path),
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

    /**
     * Upload photos for an accommodation assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $assessmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAccommodationPhotos(Request $request, Survey $survey, $assessment)
    {
        // Surveyor can only update assessments for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Get files - Laravel handles photos[0], photos[1] automatically as an array
            $photos = $request->file('photos');
            
            // If photos is null, try alternative access methods
            if (!$photos) {
                // Try getting all files and filter for photos
                $allFiles = $request->allFiles();
                $photos = [];
                
                // Check if files are in the request with 'photos' key
                if (isset($allFiles['photos']) && is_array($allFiles['photos'])) {
                    $photos = array_values($allFiles['photos']);
                } else {
                    // Try to find photos in any format
                    foreach ($allFiles as $key => $file) {
                        if (strpos($key, 'photos') !== false) {
                            if (is_array($file)) {
                                $photos = array_merge($photos, array_values($file));
                            } else {
                                $photos[] = $file;
                            }
                        }
                    }
                }
            }
            
            // Ensure photos is an array
            if (!is_array($photos)) {
                $photos = $photos ? [$photos] : [];
            }
            
            // Filter out null values and ensure we have valid files
            $photos = array_filter($photos, function($photo) {
                return $photo !== null && $photo->isValid();
            });
            
            // Re-index array to ensure sequential keys
            $photos = array_values($photos);
            
            if (empty($photos)) {
                return response()->json([
                    'error' => 'No valid photos provided. Please ensure you are uploading image files.',
                ], 422);
            }

            // Validate each photo
            foreach ($photos as $index => $photo) {
                if (!$photo->isValid()) {
                    return response()->json([
                        'error' => "Photo at index {$index} is not valid",
                    ], 422);
                }
                if ($photo->getSize() > 10240 * 1024) { // 10MB in bytes
                    return response()->json([
                        'error' => "Photo at index {$index} exceeds 10MB limit",
                    ], 422);
                }
                if (!in_array($photo->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'])) {
                    return response()->json([
                        'error' => "Photo at index {$index} is not a valid image format",
                    ], 422);
                }
            }

            // Convert assessment parameter to integer if it's not already
            $assessmentId = is_numeric($assessment) ? (int) $assessment : $assessment;
            
            // Find the accommodation assessment
            $assessmentModel = \App\Models\SurveyAccommodationAssessment::where('id', $assessmentId)
                ->where('survey_id', $survey->id)
                ->firstOrFail();

            // Get service
            $service = app(\App\Services\SurveyAccommodationDataService::class);
            
            // Save photos - ensure we pass an array
            $service->saveAccommodationPhotos($assessmentModel, array_values($photos));

            Log::info('Accommodation photos uploaded', [
                'assessment_id' => $assessmentModel->id,
                'photos_count' => count($photos),
            ]);

            // Reload photos to return them (use storage URL for absolute URLs on production)
            $assessmentModel->load('photos');
            $uploadedPhotos = $assessmentModel->photos->map(function($photo) {
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => Storage::disk('public')->url($photo->file_path),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Photos uploaded successfully',
                'photos' => $uploadedPhotos,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Assessment not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to upload accommodation photos', [
                'survey_id' => $survey->id,
                'assessment_id' => $assessment ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to upload photos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a photo from an accommodation assessment.
     * 
     * @param Request $request
     * @param Survey $survey
     * @param int $assessmentId
     * @param int $photoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccommodationPhoto(Request $request, Survey $survey, int $assessmentId, int $photoId)
    {
        // Surveyor can only delete photos for their own surveys
        if ($survey->surveyor_id && $survey->surveyor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Find the accommodation assessment
            $assessment = \App\Models\SurveyAccommodationAssessment::where('id', $assessmentId)
                ->where('survey_id', $survey->id)
                ->firstOrFail();

            // Find the photo
            $photo = \App\Models\SurveyAccommodationPhoto::where('id', $photoId)
                ->where('accommodation_assessment_id', $assessment->id)
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
            \Log::error('Failed to delete accommodation photo', [
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
