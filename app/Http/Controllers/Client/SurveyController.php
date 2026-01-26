<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Services\SurveyDataService;
use App\Services\SurveyAccommodationDataService;
use App\Services\SurveyPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{
    public function index()
    {
        // Get surveys by matching email
        $surveys = Survey::where('email_address', auth()->user()->email)
            ->orWhere('inf_field_Email', auth()->user()->email)
            ->with('surveyor')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('client.surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        // Ensure client can only view their own surveys
        $clientEmail = auth()->user()->email;
        if ($survey->email_address !== $clientEmail && $survey->inf_field_Email !== $clientEmail) {
            abort(403, 'Unauthorized');
        }
        
        return view('client.surveys.show', compact('survey'));
    }

    /**
     * Show the full assessment report for a survey.
     * 
     * @param Survey $survey
     * @return \Illuminate\View\View
     */
    public function showReport(Survey $survey)
    {
        // Ensure client can only view their own surveys
        $clientEmail = auth()->user()->email;
        if ($survey->email_address !== $clientEmail && $survey->inf_field_Email !== $clientEmail) {
            abort(403, 'Unauthorized');
        }

        // Load survey with relationships
        $survey->load('surveyor');

        // Get survey data using SurveyDataService
        $surveyDataService = app(SurveyDataService::class);
        $categories = $surveyDataService->getGroupedSurveyData($survey, false);

        // Get accommodation data using SurveyAccommodationDataService
        $accommodationDataService = app(SurveyAccommodationDataService::class);
        $accommodationSections = $accommodationDataService->getAccommodationConfigurationData($survey, false);

        // Get options mapping for display
        $optionsMapping = $surveyDataService->getOptionsMapping();

        // Get content sections
        $contentSections = $this->getContentSectionsForSurvey($survey, $categories);

        // Build desk study data (similar to surveyor's deskStudyMock method)
        $deskStudy = [
            'address' => $survey->full_address ?? $survey->property_address_full ?? 'N/A',
            'job_reference' => $survey->job_reference ?? 'N/A',
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

        // Check if accommodation types exist
        $accommodationTypesWithComponents = $accommodationDataService->getAccommodationTypesWithComponents();
        $hasAccommodationTypesWithComponents = count($accommodationTypesWithComponents) > 0;

        return view('client.surveys.report', compact(
            'survey',
            'categories',
            'accommodationSections',
            'contentSections',
            'deskStudy',
            'optionsMapping',
            'hasAccommodationTypesWithComponents'
        ));
    }

    /**
     * Download PDF report for a survey.
     * 
     * @param Survey $survey
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(Survey $survey)
    {
        // Ensure client can only download their own surveys
        $clientEmail = auth()->user()->email;
        if ($survey->email_address !== $clientEmail && $survey->inf_field_Email !== $clientEmail) {
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
            Log::error('Failed to generate PDF report for client', [
                'survey_id' => $survey->id,
                'client_email' => $clientEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Failed to generate PDF report. Please try again.');
        }
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
}











