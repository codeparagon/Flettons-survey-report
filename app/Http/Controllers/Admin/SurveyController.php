<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\SurveyDataService;
use App\Services\SurveyAccommodationDataService;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::with('surveyor')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        return view('admin.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        $surveyorRole = Role::where('name', 'surveyor')->first();
        $surveyors = User::where('role_id', $surveyorRole->id)->get();
        
        return view('admin.surveys.edit', compact('survey', 'surveyors'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'surveyor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded',
            'scheduled_date' => 'nullable|date',
            'admin_notes' => 'nullable|string',
        ]);

        $survey->update($validated);

        return redirect()->route('admin.surveys.show', $survey->id)
            ->with('success', 'Survey updated successfully.');
    }

    public function sections(Survey $survey)
    {
        // Use SurveyDataService to get grouped data
        $surveyDataService = app(SurveyDataService::class);
        
        // Use database data
        $useMockData = false;
        $categories = $surveyDataService->getGroupedSurveyData($survey, $useMockData);
        
        // Get accommodation configuration data from database
        $accommodationDataService = app(SurveyAccommodationDataService::class);
        $accommodationSections = $accommodationDataService->getAccommodationConfigurationData($survey, $useMockData);

        // Get accommodation types with components (for form dropdowns)
        $accommodationTypesWithComponents = $accommodationDataService->getAccommodationTypesWithComponents();
        
        // Check if we should show the accommodation section
        $hasAccommodationTypesWithComponents = count($accommodationTypesWithComponents) > 0;

        // Get options mapping for dynamic options from database
        $optionsMapping = $surveyDataService->getOptionsMapping();

        // Get content sections
        $contentSections = $this->getContentSectionsForSurvey($survey, $categories);

        return view('surveyor.surveys.mocks.data', compact('survey', 'categories', 'accommodationSections', 'accommodationTypesWithComponents', 'hasAccommodationTypesWithComponents', 'optionsMapping', 'contentSections'));
    }

    /**
     * Get content sections for a survey (similar to Surveyor controller method).
     */
    protected function getContentSectionsForSurvey(Survey $survey, array $categories): array
    {
        $contentSections = [
            'standalone' => [],
            'by_category' => [],
            'by_subcategory' => [],
        ];

        // Get content sections based on survey level
        if (empty($survey->level)) {
            // No level set - show all active content sections
            $allContentSections = \App\Models\SurveyContentSection::active()
                ->ordered()
                ->with(['category', 'subcategory'])
                ->get();
        } else {
            // Level is set - only show sections assigned to this level
            $surveyLevel = $this->findSurveyLevelByValue($survey->level);
            
            if (!$surveyLevel) {
                $allContentSections = collect();
            } else {
                $contentSectionIds = $surveyLevel->contentSections()->pluck('survey_content_sections.id')->unique();
                
                if ($contentSectionIds->isEmpty()) {
                    $allContentSections = collect();
                } else {
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

        foreach ($allContentSections as $section) {
            if ($section->subcategory_id) {
                $subcategoryId = $section->subcategory_id;
                if (!isset($contentSections['by_subcategory'][$subcategoryId])) {
                    $contentSections['by_subcategory'][$subcategoryId] = [];
                }
                $contentSections['by_subcategory'][$subcategoryId][] = $section;
            } elseif ($section->category_id) {
                $categoryId = $section->category_id;
                if (!isset($contentSections['by_category'][$categoryId])) {
                    $contentSections['by_category'][$categoryId] = [];
                }
                $contentSections['by_category'][$categoryId][] = $section;
            } else {
                $contentSections['standalone'][] = $section;
            }
        }

        return $contentSections;
    }

    /**
     * Find SurveyLevel by matching survey level value.
     */
    protected function findSurveyLevelByValue($levelValue)
    {
        if (empty($levelValue)) {
            return null;
        }

        // Try exact match first
        $level = \App\Models\SurveyLevel::where('name', $levelValue)
            ->orWhere('display_name', $levelValue)
            ->first();

        if ($level) {
            return $level;
        }

        // Try to extract level number from strings like "Level 1", "level_1", etc.
        if (preg_match('/level[\s_]*(\d+)/i', $levelValue, $matches)) {
            $levelNumber = $matches[1];
            $level = \App\Models\SurveyLevel::where('name', 'like', "%{$levelNumber}%")
                ->orWhere('display_name', 'like', "%{$levelNumber}%")
                ->first();
            
            if ($level) {
                return $level;
            }
        }

        return null;
    }
}











