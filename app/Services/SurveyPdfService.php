<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyLevel;
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
        
        // Collect all survey images
        $surveyImages = $this->collectAllSurveyImages($categories, $accommodationSections);
        
        return [
            'categories' => $categories,
            'accommodationSections' => $accommodationSections,
            'contentSections' => $contentSections,
            'surveyImages' => $surveyImages,
        ];
    }

    /**
     * Collect all images from sections and accommodations for PDF.
     * 
     * @param array $categories
     * @param array $accommodationSections
     * @return array
     */
    protected function collectAllSurveyImages(array $categories, array $accommodationSections): array
    {
        $images = [];
        
        // Collect images from regular sections
        foreach ($categories as $categoryName => $subCategories) {
            foreach ($subCategories as $subCategoryName => $sections) {
                foreach ($sections as $section) {
                    if (!empty($section['photos']) && is_array($section['photos']) && count($section['photos']) > 0) {
                        $sectionId = $section['id'] ?? null;
                        $anchorId = $sectionId ? 'images-section-' . $sectionId : 'images-section-' . md5($section['name']);
                        
                        $images[] = [
                            'type' => 'section',
                            'id' => $sectionId,
                            'anchor_id' => $anchorId,
                            'name' => $section['name'] ?? 'Unknown Section',
                            'photos' => $section['photos'],
                        ];
                    }
                }
            }
        }
        
        // Collect images from accommodation sections
        foreach ($accommodationSections as $accommodation) {
            if (!empty($accommodation['photos']) && is_array($accommodation['photos']) && count($accommodation['photos']) > 0) {
                $accommodationId = $accommodation['id'] ?? null;
                $anchorId = $accommodationId ? 'images-accommodation-' . $accommodationId : 'images-accommodation-' . md5($accommodation['name']);
                
                $images[] = [
                    'type' => 'accommodation',
                    'id' => $accommodationId,
                    'anchor_id' => $anchorId,
                    'name' => $accommodation['name'] ?? ($accommodation['accommodation_type_name'] ?? 'Unknown Accommodation'),
                    'photos' => $accommodation['photos'],
                ];
            }
        }
        
        return $images;
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
        $level = SurveyLevel::where('name', $levelValue)->first();
        if ($level) {
            return $level;
        }
        
        // Try exact match on display_name
        $level = SurveyLevel::where('display_name', $levelValue)->first();
        if ($level) {
            return $level;
        }
        
        // Try to extract level number and match (e.g., "Level 1" -> "level_1")
        // Extract number from "Level 1", "level_1", "Level 1 - Condition Report", etc.
        if (preg_match('/level[_\s]*(\d+)/i', $levelValue, $matches)) {
            $levelNumber = $matches[1];
            $normalizedName = 'level_' . $levelNumber;
            $level = SurveyLevel::where('name', $normalizedName)->first();
            if ($level) {
                return $level;
            }
        }
        
        return null;
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

        // Get content sections based on survey level
        // If survey has no level set (null/empty), show all sections for backward compatibility
        // If survey has a level set, only show sections assigned to that level
        
        if (empty($survey->level)) {
            // No level set - show all active content sections (backward compatibility for old surveys)
            $allContentSections = SurveyContentSection::active()
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
                    $allContentSections = SurveyContentSection::whereIn('id', $contentSectionIds)
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
            'surveyImages' => $data['surveyImages'],
        ]);
        
        // Set PDF options for UK A4 format
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        
        return $pdf;
    }
}
