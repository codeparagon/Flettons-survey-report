<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveySectionAssessment;
use Illuminate\Support\Collection;

class SurveyDataService
{
    /**
     * Get survey data grouped by category with section grouping for clones.
     * 
     * @param Survey $survey
     * @param bool $useMockData Whether to use mock data or real database data
     * @return array
     */
    public function getGroupedSurveyData(Survey $survey, bool $useMockData = true): array
    {
        if ($useMockData) {
            return $this->getMockGroupedData($survey);
        }
        
        return $this->getRealGroupedData($survey);
    }

    /**
     * Get real section assessments grouped by category.
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getRealGroupedData(Survey $survey): array
    {
        // Load required sections for this survey level
        $requiredSections = $survey->getRequiredSections();
        
        // Load assessments with relationships
        $assessments = $survey->sectionAssessments()
            ->with(['section.category'])
            ->get()
            ->keyBy('survey_section_id');
        
        // Group sections by category
        $categoriesRaw = [];
        
        foreach ($requiredSections as $section) {
            $categoryName = $section->category->display_name ?? 'Uncategorized';
            $assessment = $assessments->get($section->id);
            
            // Transform assessment to view format
            $sectionData = $this->transformAssessmentToViewFormat($section, $assessment);
            
            if (!isset($categoriesRaw[$categoryName])) {
                $categoriesRaw[$categoryName] = [];
            }
            
            $categoriesRaw[$categoryName][] = $sectionData;
        }
        
        // Group by sub-category and create structure
        return $this->groupSectionsBySubCategory($categoriesRaw);
    }

    /**
     * Get mock data grouped by category (for UI development).
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getMockGroupedData(Survey $survey): array
    {
        $categoriesRaw = $this->getMockData($survey);
        return $this->groupSectionsBySubCategory($categoriesRaw);
    }

    /**
     * Transform a section assessment to view format.
     * 
     * @param \App\Models\SurveySection $section
     * @param SurveySectionAssessment|null $assessment
     * @return array
     */
    protected function transformAssessmentToViewFormat($section, $assessment = null): array
    {
        // Get data from assessment or use defaults
        $additionalData = $assessment && $assessment->additional_data 
            ? (is_array($assessment->additional_data) ? $assessment->additional_data : [])
            : [];
        
        // Calculate completion (simplified - can be enhanced)
        $completion = $this->calculateCompletion($assessment);
        $total = 10; // Can be made dynamic based on section fields
        
        // Handle cloned sections - get name from additional_data if it's a clone
        $sectionName = $section->display_name ?? $section->name;
        if ($assessment && isset($additionalData['clone_name'])) {
            $sectionName = $additionalData['clone_name'];
        } elseif ($assessment && isset($additionalData['variant_section'])) {
            // Build name from base name + variant
            $baseName = $this->extractBaseName($section->display_name ?? $section->name);
            $variant = $additionalData['variant_section'];
            $sectionName = $baseName . ' [' . $variant . ']';
        }
        
        // Get selected section - prioritize additional_data, fallback to section name
        $selectedSection = $additionalData['selected_section'] ?? 
                          $additionalData['variant_section'] ?? 
                          $section->display_name ?? 
                          $section->name;
        
        // Get photos from additional_data or empty array
        $photos = $additionalData['photos'] ?? [];
        // If photos is stored as JSON string, decode it
        if (is_string($photos)) {
            $photos = json_decode($photos, true) ?? [];
        }
        
        // Get subcategory_key from section's field_config or additional_data, or derive from section name
        $subcategoryKey = null;
        if ($section->field_config && isset($section->field_config['subcategory_key'])) {
            $subcategoryKey = $section->field_config['subcategory_key'];
        } elseif (isset($additionalData['subcategory_key'])) {
            $subcategoryKey = $additionalData['subcategory_key'];
        } else {
            // Fallback: try to derive from section name using mapping
            $baseName = $this->extractBaseName($section->display_name ?? $section->name);
            $categoryName = $section->category->display_name ?? '';
            $subCategoriesMapping = $this->getSubCategoriesMapping();
            $subCategories = $subCategoriesMapping[$categoryName] ?? [];
            
            // Try to find matching subcategory by name
            foreach ($subCategories as $subCategory) {
                if (stripos($baseName, $subCategory['display_name']) !== false || 
                    stripos($subCategory['display_name'], $baseName) !== false) {
                    $subcategoryKey = $subCategory['key'];
                    break;
                }
            }
        }
        
        return [
            'id' => $assessment ? $assessment->id : 'new_' . $section->id,
            'section_id' => $section->id,
            'name' => $sectionName,
            'subcategory_key' => $subcategoryKey,
            'completion' => $completion,
            'total' => $total,
            'condition_rating' => $this->mapConditionRating($assessment->condition_rating ?? null),
            'selected_section' => $selectedSection,
            'location' => $additionalData['location'] ?? '',
            'structure' => $additionalData['structure'] ?? '',
            'material' => $assessment->material ?? $additionalData['material'] ?? '',
            'defects' => $assessment->defects ?? $additionalData['defects'] ?? [],
            'remaining_life' => $assessment->remaining_life ?? $additionalData['remaining_life'] ?? '',
            'costs' => $additionalData['costs'] ?? [],
            'notes' => $assessment->notes ?? $additionalData['notes'] ?? '',
            'photos' => $photos, // For photo count display
        ];
    }

    /**
     * Calculate completion percentage for an assessment.
     * 
     * @param SurveySectionAssessment|null $assessment
     * @return int
     */
    protected function calculateCompletion($assessment): int
    {
        if (!$assessment) {
            return 0;
        }
        
        // Simple calculation - can be enhanced
        $fields = 0;
        $completed = 0;
        
        if ($assessment->material) $fields++;
        if ($assessment->defects && count($assessment->defects) > 0) $fields++;
        if ($assessment->remaining_life) $fields++;
        if ($assessment->notes) $fields++;
        if ($assessment->report_content) $fields++;
        
        if ($assessment->material) $completed++;
        if ($assessment->defects && count($assessment->defects) > 0) $completed++;
        if ($assessment->remaining_life) $completed++;
        if ($assessment->notes) $completed++;
        if ($assessment->report_content) $completed++;
        
        return $fields > 0 ? (int)round(($completed / $fields) * 10) : 0;
    }

    /**
     * Map condition rating to numeric value.
     * 
     * @param string|null $rating
     * @return int
     */
    protected function mapConditionRating($rating): int
    {
        $mapping = [
            'excellent' => 1,
            'good' => 2,
            'fair' => 3,
            'poor' => 3,
        ];
        
        return $mapping[$rating] ?? 2;
    }

    /**
     * Get sub-categories mapping for each category.
     * This defines which sub-categories exist under each main category.
     * Each subcategory has a key (stable identifier) and display_name (changeable).
     * 
     * @return array
     */
    protected function getSubCategoriesMapping(): array
    {
        return [
            'Exterior' => [
                ['key' => 'roofing', 'display_name' => 'Roofing'],
                ['key' => 'chimneys', 'display_name' => 'Chimneys, Pots and Stacks'],
                ['key' => 'walls', 'display_name' => 'Walls'],
                ['key' => 'windows', 'display_name' => 'Windows'],
                ['key' => 'doors', 'display_name' => 'Doors'],
                ['key' => 'gutters', 'display_name' => 'Gutters and Downpipes'],
                ['key' => 'external_joinery', 'display_name' => 'External Joinery'],
            ],
            'Interior' => [
                ['key' => 'roof_void', 'display_name' => 'Roof Void'],
                ['key' => 'ceilings', 'display_name' => 'Ceilings'],
                ['key' => 'internal_walls', 'display_name' => 'Internal walls'],
                ['key' => 'floors', 'display_name' => 'Floors'],
                ['key' => 'internal_doors', 'display_name' => 'Internal Doors'],
                ['key' => 'internal_joinery', 'display_name' => 'Internal Joinery'],
            ],
            'Building Services' => [
                ['key' => 'fire_smoke_alarms', 'display_name' => 'Fire and Smoke Alarms'],
                ['key' => 'water_supply', 'display_name' => 'Water Supply and Fittings'],
                ['key' => 'electricity_supply', 'display_name' => 'Electricity Supply and Fittings'],
                ['key' => 'heating', 'display_name' => 'Heating'],
                ['key' => 'ventilation', 'display_name' => 'Ventilation'],
            ],
            'Damp & Timber and Structural Defects' => [
                ['key' => 'high_moisture', 'display_name' => 'High Moisture and Locations'],
                ['key' => 'timber_defects', 'display_name' => 'Timber Defects and Locations'],
                ['key' => 'structural_defects', 'display_name' => 'Structural Defects and Locations'],
            ],
        ];
    }

    /**
     * Group sections by sub-category using key-based mapping.
     * 
     * @param array $categoriesRaw
     * @return array
     */
    protected function groupSectionsBySubCategory(array $categoriesRaw): array
    {
        $subCategoriesMapping = $this->getSubCategoriesMapping();
        
        // Create a lookup map: category => [key => display_name]
        $subCategoryLookup = [];
        foreach ($subCategoriesMapping as $categoryName => $subCategories) {
            $subCategoryLookup[$categoryName] = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryLookup[$categoryName][$subCategory['key']] = $subCategory['display_name'];
            }
        }

        $categories = [];
        foreach ($categoriesRaw as $categoryName => $sections) {
            // Get sub-categories for this category
            $subCategories = $subCategoriesMapping[$categoryName] ?? [];
            $lookup = $subCategoryLookup[$categoryName] ?? [];
            
            // Initialize sub-category arrays using keys
            $grouped = [];
            foreach ($subCategories as $subCategory) {
                $grouped[$subCategory['key']] = [];
            }
            
            // Group sections by subcategory_key
            foreach ($sections as $section) {
                $subCategoryKey = $section['subcategory_key'] ?? null;
                
                if ($subCategoryKey && isset($grouped[$subCategoryKey])) {
                    // Section has a valid subcategory_key, add it to that group
                    $grouped[$subCategoryKey][] = $section;
                } else {
                    // No subcategory_key or key not found, create fallback subcategory
                    // Use base name as fallback key
                    $extractBaseName = function($name) {
                        $baseName = preg_replace('/\s*\([^)]*\)\s*/', '', $name);
                        $baseName = preg_replace('/\s*\[[^\]]*\]\s*/', '', $baseName);
                        return trim($baseName);
                    };
                    $fallbackKey = strtolower(str_replace(' ', '_', $extractBaseName($section['name'])));
                    
                    if (!isset($grouped[$fallbackKey])) {
                        $grouped[$fallbackKey] = [];
                    }
                    $grouped[$fallbackKey][] = $section;
                }
            }
            
            // Convert grouped structure to use display_name as key for output
            $finalGrouped = [];
            foreach ($grouped as $key => $subCategorySections) {
                if (count($subCategorySections) > 0) {
                    // Use display_name from lookup if available, otherwise use key
                    $displayName = $lookup[$key] ?? ucfirst(str_replace('_', ' ', $key));
                    $finalGrouped[$displayName] = $subCategorySections;
                }
            }
            
            $categories[$categoryName] = $finalGrouped;
        }

        return $categories;
    }

    /**
     * Get mock data for UI development.
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getMockData(Survey $survey): array
    {
        return [
            'Exterior' => [
                [
                    'id' => 1,
                    'name' => 'Roofing (Main)',
                    'subcategory_key' => 'roofing',
                    'completion' => 2,
                    'total' => 10,
                    'condition_rating' => 3,
                    'selected_section' => 'Main Roof',
                    'location' => 'Whole Property',
                    'structure' => 'Pitched',
                    'material' => 'Slate',
                    'defects' => ['Deflection'],
                    'remaining_life' => '0',
                    'costs' => [
                        ['category' => 'Essential', 'description' => 'Commission a roofer to acc', 'due' => '2026', 'cost' => '300']
                    ],
                    'notes' => 'The moss was mainly to the front of the roof.',
                ],
                [
                    'id' => 3,
                    'name' => 'Chimneys, Pots and Stackss',
                    'subcategory_key' => 'chimneys',
                    'completion' => 10,
                    'total' => 10,
                    'condition_rating' => 3,
                    'selected_section' => 'Main Roof',
                    'location' => 'Whole Property',
                    'structure' => 'Pitched',
                    'material' => 'Slate',
                    'defects' => ['None'],
                    'remaining_life' => '10+',
                ],
            ],
            'Interior' => [
                [
                    'id' => 4,
                    'name' => 'Roof Void',
                    'subcategory_key' => 'roof_void',
                    'completion' => 10,
                    'total' => 10,
                    'condition_rating' => 2,
                    'selected_section' => 'Roof Void',
                    'location' => 'Whole Property',
                    'structure' => 'Pitched',
                    'material' => 'Timber',
                    'defects' => ['None'],
                    'remaining_life' => '10+',
                    'costs' => [],
                    'notes' => '',
                ],
                [
                    'id' => 5,
                    'name' => 'Ceilings',
                    'subcategory_key' => 'ceilings',
                    'completion' => 0,
                    'total' => 10,
                    'condition_rating' => 2,
                    'selected_section' => 'Ceilings',
                    'location' => 'Whole Property',
                    'structure' => 'Flat',
                    'material' => 'Plasterboard',
                    'defects' => [],
                    'remaining_life' => '',
                    'costs' => [],
                    'notes' => '',
                ],
                [
                    'id' => 6,
                    'name' => 'Internal walls',
                    'subcategory_key' => 'internal_walls',
                    'completion' => 10,
                    'total' => 10,
                    'condition_rating' => 1,
                    'selected_section' => 'Internal walls',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Plaster',
                    'defects' => ['None'],
                    'remaining_life' => '10+',
                    'costs' => [],
                    'notes' => '',
                ],
            ],
            'Building Services' => [
                [
                    'id' => 7,
                    'name' => 'Fire and Smoke Alarms',
                    'subcategory_key' => 'fire_smoke_alarms',
                    'completion' => 2,
                    'total' => 10,
                    'condition_rating' => 3,
                    'selected_section' => 'Fire and Smoke Alarms',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Mixed',
                    'defects' => [],
                    'remaining_life' => '',
                    'costs' => [],
                    'notes' => '',
                ],
                [
                    'id' => 8,
                    'name' => 'Water Supply and Fittings',
                    'subcategory_key' => 'water_supply',
                    'completion' => 2,
                    'total' => 10,
                    'condition_rating' => 3,
                    'selected_section' => 'Water Supply',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Copper',
                    'defects' => [],
                    'remaining_life' => '',
                    'costs' => [],
                    'notes' => '',
                ],
                [
                    'id' => 9,
                    'name' => 'Electricity Supply and Fittings',
                    'subcategory_key' => 'electricity_supply',
                    'completion' => 10,
                    'total' => 10,
                    'condition_rating' => 3,
                    'selected_section' => 'Electricity Supply',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Copper',
                    'defects' => ['None'],
                    'remaining_life' => '10+',
                    'costs' => [],
                    'notes' => '',
                ],
            ],
            'Damp & Timber and Structural Defects' => [
                [
                    'id' => 10,
                    'name' => 'High Moisture and Locations',
                    'subcategory_key' => 'high_moisture',
                    'completion' => 10,
                    'total' => 10,
                    'condition_rating' => 2,
                    'selected_section' => 'High Moisture',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Mixed',
                    'defects' => ['None'],
                    'remaining_life' => '10+',
                    'costs' => [],
                    'notes' => '',
                ],
                [
                    'id' => 11,
                    'name' => 'Timber Defects and Locations',
                    'subcategory_key' => 'timber_defects',
                    'completion' => 0,
                    'total' => 10,
                    'condition_rating' => 2,
                    'selected_section' => 'Timber Defects',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Timber',
                    'defects' => [],
                    'remaining_life' => '',
                    'costs' => [],
                    'notes' => '',
                ],
                [
                    'id' => 12,
                    'name' => 'Structural Defects and Locations',
                    'subcategory_key' => 'structural_defects',
                    'completion' => 10,
                    'total' => 10,
                    'condition_rating' => 1,
                    'selected_section' => 'Structural Defects',
                    'location' => 'Whole Property',
                    'structure' => 'Standard',
                    'material' => 'Mixed',
                    'defects' => ['None'],
                    'remaining_life' => '10+',
                    'costs' => [],
                    'notes' => '',
                ],
            ],
        ];
    }

    /**
     * Create a cloned section assessment from an existing one.
     * 
     * Note: Since there's a unique constraint on survey_id + survey_section_id,
     * clones are stored as separate records with clone metadata in additional_data.
     * 
     * @param Survey $survey
     * @param SurveySectionAssessment $sourceAssessment
     * @param string $selectedSection The selected section name for the clone
     * @param string|null $customName Optional custom name for the clone
     * @return SurveySectionAssessment
     */
    public function cloneSectionAssessment(
        Survey $survey, 
        SurveySectionAssessment $sourceAssessment, 
        string $selectedSection,
        ?string $customName = null
    ): SurveySectionAssessment {
        $section = $sourceAssessment->section;
        $baseName = $this->extractBaseName($section->display_name ?? $section->name);
        $newName = $customName ?? ($baseName . " [" . $selectedSection . "]");
        
        // Get additional data from source
        $sourceAdditionalData = $sourceAssessment->additional_data ?? [];
        if (!is_array($sourceAdditionalData)) {
            $sourceAdditionalData = [];
        }
        
        // Prepare additional data for clone
        $additionalData = array_merge($sourceAdditionalData, [
            'selected_section' => $selectedSection,
            'cloned_from' => $sourceAssessment->id,
            'clone_name' => $newName,
            'variant_section' => $selectedSection,
            'is_clone' => true,
        ]);
        
        // Create new assessment
        // Note: If unique constraint is an issue, you may need to:
        // 1. Remove the unique constraint and allow multiple assessments per section
        // 2. Or use a different approach like a separate clones table
        $clonedAssessment = SurveySectionAssessment::create([
            'survey_id' => $survey->id,
            'survey_section_id' => $section->id,
            'condition_rating' => $sourceAssessment->condition_rating,
            'material' => $sourceAssessment->material,
            'defects' => $sourceAssessment->defects,
            'remaining_life' => $sourceAssessment->remaining_life,
            'notes' => $sourceAssessment->notes,
            'report_content' => $sourceAssessment->report_content,
            'is_completed' => false,
            'additional_data' => $additionalData,
        ]);
        
        return $clonedAssessment;
    }

    /**
     * Extract base name from section name (removes parentheses and brackets).
     * 
     * @param string $name
     * @return string
     */
    public function extractBaseName(string $name): string
    {
        $baseName = preg_replace('/\s*\([^)]*\)\s*/', '', $name);
        $baseName = preg_replace('/\s*\[[^\]]*\]\s*/', '', $baseName);
        return trim($baseName);
    }
}

