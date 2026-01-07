<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyContentSection;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use Barryvdh\DomPDF\Facade\Pdf;

class SurveyPdfService
{
    protected SurveyDataService $surveyDataService;
    protected SurveyAccommodationDataService $accommodationDataService;

    public function __construct(
        SurveyDataService $surveyDataService,
        SurveyAccommodationDataService $accommodationDataService
    ) {
        $this->surveyDataService = $surveyDataService;
        $this->accommodationDataService = $accommodationDataService;
    }

    /**
     * Collect all section data for PDF generation.
     * 
     * @param Survey $survey
     * @return array
     */
    public function collectAllSectionData(Survey $survey): array
    {
        // Get all regular sections with their data
        $categories = $this->surveyDataService->getGroupedSurveyData($survey, false);
        
        // Get all accommodation sections with their data
        $accommodationSections = $this->accommodationDataService->getAccommodationConfigurationData($survey, false);
        
        // Get all content sections directly from database (to ensure latest content)
        $contentSections = $this->getContentSectionsForSurvey($survey, $categories);
        
        return [
            'categories' => $categories,
            'accommodationSections' => $accommodationSections,
            'contentSections' => $contentSections,
        ];
    }

    /**
     * Get content sections for a survey, grouped by their link type.
     * This matches the controller's logic but fetches fresh data.
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

        // Get all active content sections directly from database (fresh query)
        $allContentSections = SurveyContentSection::active()
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
                $category = SurveyCategory::where('display_name', $categoryName)->first();
                $subcategory = SurveySubcategory::where('display_name', $subCategoryName)->first();
                
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
     * Generate PDF from survey data.
     * 
     * @param Survey $survey
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePdf(Survey $survey)
    {
        // Collect all section data
        $data = $this->collectAllSectionData($survey);
        
        // Load the survey with relationships for PDF view
        $survey->load('surveyor');
        
        // Generate PDF using the view
        $pdf = Pdf::loadView('surveyor.surveys.pdf.report', [
            'survey' => $survey,
            'categories' => $data['categories'],
            'accommodationSections' => $data['accommodationSections'],
            'contentSections' => $data['contentSections'],
        ]);
        
        // Set PDF options for UK A4 format
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        
        return $pdf;
    }
}
