<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveySectionAssessment;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use App\Models\SurveySectionDefinition;
use App\Models\SurveyOptionType;
use App\Models\SurveyOption;
use App\Models\SurveySectionCost;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SurveyDataService
{
    protected ChatGPTService $chatGPTService;

    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }
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
     * Get real section assessments grouped by category from database.
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getRealGroupedData(Survey $survey): array
    {
        // Get all section definitions with their relationships
        $sectionDefinitions = SurveySectionDefinition::with(['subcategory.category'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // Load existing assessments for this survey
        $assessments = SurveySectionAssessment::where('survey_id', $survey->id)
            ->with([
                'sectionDefinition.subcategory.category', 
                'sectionType', 
                'location', 
                'structure', 
                'material', 
                'remainingLife', 
                'defects', 
                'photos' => function($query) {
                    $query->orderBy('sort_order');
                }, 
                'costs'
            ])
            ->get()
            ->groupBy('section_definition_id');
        
        // Group sections by category and subcategory
        $categoriesRaw = [];
        
        foreach ($sectionDefinitions as $sectionDef) {
            $categoryName = $sectionDef->subcategory->category->display_name ?? 'Uncategorized';
            $subcategoryName = $sectionDef->subcategory->display_name ?? '';
            $subcategoryKey = $sectionDef->subcategory->name ?? '';
            
            // Get assessments for this section definition
            $sectionAssessments = $assessments->get($sectionDef->id, collect());
            
            // If no assessments exist, create one default entry
            if ($sectionAssessments->isEmpty()) {
                $sectionData = $this->transformSectionDefinitionToViewFormat($sectionDef, null);
                $sectionData['subcategory_key'] = $subcategoryKey;
            
            if (!isset($categoriesRaw[$categoryName])) {
                $categoriesRaw[$categoryName] = [];
            }
                $categoriesRaw[$categoryName][] = $sectionData;
            } else {
                // Transform each assessment
                foreach ($sectionAssessments as $assessment) {
                    $sectionData = $this->transformAssessmentToViewFormat($sectionDef, $assessment);
                    $sectionData['subcategory_key'] = $subcategoryKey;
                    
                    if (!isset($categoriesRaw[$categoryName])) {
                        $categoriesRaw[$categoryName] = [];
                    }
            $categoriesRaw[$categoryName][] = $sectionData;
                }
            }
        }
        
        // Group by sub-category using the existing method
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
     * Transform a section definition to view format (when no assessment exists).
     * 
     * @param SurveySectionDefinition $sectionDef
     * @param SurveySectionAssessment|null $assessment
     * @return array
     */
    protected function transformSectionDefinitionToViewFormat(SurveySectionDefinition $sectionDef, $assessment = null): array
    {
        $subcategoryKey = $sectionDef->subcategory->name ?? '';
        
        // Build section name - if assessment has section_type, use it
        $sectionName = $sectionDef->display_name;
        if ($assessment && $assessment->sectionType) {
            $sectionName = $sectionDef->display_name . ' [' . $assessment->sectionType->value . ']';
        }
        
        // Calculate completion count based on submitted fields
        $completionData = $this->calculateCompletionCount($assessment);
        
        return [
            'id' => $assessment ? $assessment->id : 'new_' . $sectionDef->id,
            'section_id' => $sectionDef->id,
            'name' => $sectionName,
            'subcategory_key' => $subcategoryKey,
            'completion' => $completionData['count'],
            'total' => $completionData['total'],
            'condition_rating' => $assessment ? $this->mapConditionRatingFromNumeric($assessment->condition_rating) : 'ni',
            'selected_section' => $assessment && $assessment->sectionType ? $assessment->sectionType->value : '',
            'location' => $assessment && $assessment->location ? $assessment->location->value : '',
            'structure' => $assessment && $assessment->structure ? $assessment->structure->value : '',
            'material' => $assessment && $assessment->material ? $assessment->material->value : '',
            'defects' => $assessment ? $assessment->defects->pluck('value')->toArray() : [],
            'remaining_life' => $assessment && $assessment->remainingLife ? $assessment->remainingLife->value : '',
            'costs' => $assessment ? $assessment->costs->map(function($cost) {
                return [
                    'category' => $cost->category,
                    'description' => $cost->description,
                    'due' => $cost->due_year ? (string)$cost->due_year : '',
                    'cost' => number_format($cost->amount, 2),
                ];
            })->toArray() : [],
            'notes' => $assessment ? $assessment->notes : '',
            'photos' => $assessment && $assessment->photos ? $assessment->photos->sortBy('sort_order')->map(function($photo) {
                // Generate full URL using asset() helper to include base URL
                $url = asset('storage/' . ltrim($photo->file_path, '/'));
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => $url,
                ];
            })->values()->toArray() : [],
            'report_content' => $assessment && $assessment->report_content ? $assessment->report_content : null,
            'has_report' => $assessment && !empty(trim($assessment->report_content ?? '')),
        ];
    }

    /**
     * Transform a section assessment to view format.
     * 
     * @param SurveySectionDefinition $sectionDef
     * @param SurveySectionAssessment|null $assessment
     * @return array
     */
    protected function transformAssessmentToViewFormat($sectionDef, $assessment = null): array
    {
        return $this->transformSectionDefinitionToViewFormat($sectionDef, $assessment);
    }
    
    /**
     * Map condition rating from numeric to string format.
     * 
     * @param int|null $rating
     * @return string
     */
    protected function mapConditionRatingFromNumeric($rating): string
    {
        if ($rating === null) {
            return 'ni';
        }
        
        return (string)$rating;
    }

    /**
     * Calculate completion count based on submitted fields.
     * Counts all fields that have been submitted to the database.
     * Notes, costs, and photos are counted as 1 if they exist, 0 if they don't.
     * Notes are excluded if empty.
     * 
     * @param SurveySectionAssessment|null $assessment
     * @return array ['count' => int, 'total' => int]
     */
    protected function calculateCompletionCount($assessment): array
    {
        if (!$assessment) {
            return ['count' => 0, 'total' => 10];
        }
        
        $count = 0;
        
        // Count basic fields (1 point each if filled)
        if ($assessment->section_type_id) $count++;
        if ($assessment->location_id) $count++;
        if ($assessment->structure_id) $count++;
        if ($assessment->material_id) $count++;
        if ($assessment->remaining_life_id) $count++;
        if ($assessment->condition_rating !== null) $count++;
        
        // Count defects (1 point if any defects exist)
        if ($assessment->defects()->count() > 0) $count++;
        
        // Count notes (1 point if notes exist and are not empty) - EXCLUDE if empty
        $hasNotes = !empty(trim($assessment->notes ?? ''));
        if ($hasNotes) $count++;
        
        // Count costs (1 point if any costs exist, 0 if no costs) - similar to notes
        $hasCosts = $assessment->costs()->count() > 0;
        if ($hasCosts) $count++;
        
        // Count photos (1 point if any photos exist, 0 if no photos) - similar to notes
        $hasPhotos = $assessment->photos()->count() > 0;
        if ($hasPhotos) $count++;
        
        // Calculate total: base fields (7) + notes (0 or 1) + costs (0 or 1) + photos (0 or 1)
        // Base fields: section_type, location, structure, material, remaining_life, condition_rating, defects = 7
        $baseFields = 7;
        $notesField = $hasNotes ? 1 : 0;
        $costsField = $hasCosts ? 1 : 0;
        $photosField = $hasPhotos ? 1 : 0;
        
        // Total is always 10: 7 base fields + 3 optional fields (notes, costs, photos)
        $total = $baseFields + 3; // 7 + 3 = 10
        
        return [
            'count' => $count,
            'total' => $total
        ];
    }

    /**
     * Calculate completion percentage for an assessment (kept for backward compatibility).
     * 
     * @param SurveySectionAssessment|null $assessment
     * @return int
     */
    protected function calculateCompletion($assessment): int
    {
        $completionData = $this->calculateCompletionCount($assessment);
        return $completionData['count'];
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
        $categories = SurveyCategory::with(['subcategories' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        $mapping = [];
        foreach ($categories as $category) {
            $mapping[$category->display_name] = $category->subcategories->map(function($subcategory) {
        return [
                    'key' => $subcategory->name,
                    'display_name' => $subcategory->display_name,
                ];
            })->toArray();
        }

        return $mapping;
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


    /**
     * Get all options mapping in a structured format from database.
     * 
     * @return array
     */
    public function getOptionsMapping(): array
    {
        $mapping = [
            // Global options
            'location' => $this->getLocationOptions(),
            'remaining_life' => $this->getRemainingLifeOptions(),
            'defects' => $this->getDefectOptions(),
        ];

        // Get category-based options
        $categories = SurveyCategory::where('is_active', true)->orderBy('sort_order')->get();
        foreach ($categories as $category) {
            $mapping[$category->display_name] = [
                'section' => $this->getSectionOptions($category->display_name),
                'structure' => $this->getStructureOptions($category->display_name),
                'material' => $this->getMaterialOptions($category->display_name),
            ];
        }

        return $mapping;
    }

    /**
     * Get section options based on category and subcategory from database.
     * 
     * @param string $categoryName
     * @param string|null $subCategoryKey
     * @return array
     */
    public function getSectionOptions(string $categoryName, ?string $subCategoryKey = null): array
    {
        $sectionType = SurveyOptionType::where('key_name', 'section_type')->first();
        if (!$sectionType) {
            return [];
        }

        $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
        if (!$category) {
            return [];
        }

        $query = SurveyOption::where('option_type_id', $sectionType->id)
            ->where('is_active', true);

        if ($subCategoryKey) {
            $subcategory = SurveySubcategory::where('category_id', $category->id)
                ->where('name', $subCategoryKey)
                ->first();
            if ($subcategory) {
                $query->where('scope_type', 'subcategory')
                    ->where('scope_id', $subcategory->id);
            } else {
        return [];
            }
        } else {
            // Get all subcategory-scoped options for this category
            $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
                ->pluck('id')
                ->toArray();
            $query->where('scope_type', 'subcategory')
                ->whereIn('scope_id', $subcategoryIds);
        }

        return $query->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Get location options (global) from database.
     * 
     * @return array
     */
    public function getLocationOptions(): array
    {
        $locationType = SurveyOptionType::where('key_name', 'location')->first();
        if (!$locationType) {
            return [];
        }

        return SurveyOption::where('option_type_id', $locationType->id)
            ->where('scope_type', 'global')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Get structure options based on category from database.
     * 
     * @param string $categoryName
     * @param string|null $subCategoryKey
     * @return array
     */
    public function getStructureOptions(string $categoryName, ?string $subCategoryKey = null): array
    {
        $structureType = SurveyOptionType::where('key_name', 'structure')->first();
        if (!$structureType) {
            return ['Standard'];
        }

        $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
        if (!$category) {
            return ['Standard'];
        }

        return SurveyOption::where('option_type_id', $structureType->id)
            ->where('scope_type', 'category')
            ->where('scope_id', $category->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray() ?: ['Standard'];
    }

    /**
     * Get material options based on category from database.
     * 
     * @param string $categoryName
     * @param string|null $subCategoryKey
     * @return array
     */
    public function getMaterialOptions(string $categoryName, ?string $subCategoryKey = null): array
    {
        $materialType = SurveyOptionType::where('key_name', 'material')->first();
        if (!$materialType) {
            return ['Mixed'];
        }

        $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
        if (!$category) {
            return ['Mixed'];
        }

        return SurveyOption::where('option_type_id', $materialType->id)
            ->where('scope_type', 'category')
            ->where('scope_id', $category->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray() ?: ['Mixed'];
    }

    /**
     * Get defect options from database (global for now, can be category-specific in future).
     * 
     * @param string|null $categoryName
     * @return array
     */
    public function getDefectOptions(?string $categoryName = null): array
    {
        $defectType = SurveyOptionType::where('key_name', 'defects')->first();
        if (!$defectType) {
            return [];
        }

        return SurveyOption::where('option_type_id', $defectType->id)
            ->where('scope_type', 'global')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Get remaining life options (global) from database.
     * 
     * @return array
     */
    public function getRemainingLifeOptions(): array
    {
        $remainingLifeType = SurveyOptionType::where('key_name', 'remaining_life')->first();
        if (!$remainingLifeType) {
            return [];
        }

        return SurveyOption::where('option_type_id', $remainingLifeType->id)
            ->where('scope_type', 'global')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    // ============================================================================
    // SECTION ASSESSMENT METHODS
    // ============================================================================

    /**
     * Save section assessment and generate report.
     * 
     * @param Survey $survey
     * @param SurveySectionDefinition $sectionDefinition
     * @param array $formData Form data from frontend
     * @param bool $isClone Whether this is a cloned section
     * @param int|null $assessmentId If provided, update this specific assessment (for updates)
     * @return array ['success' => bool, 'assessment' => SurveySectionAssessment, 'report_content' => string]
     */
    public function saveSectionAssessment(Survey $survey, SurveySectionDefinition $sectionDefinition, array $formData, bool $isClone = false, ?int $assessmentId = null): array
    {
        DB::beginTransaction();
        
        try {
            // If assessment ID is provided, update that specific assessment
            if ($assessmentId) {
                $assessment = SurveySectionAssessment::where('id', $assessmentId)
                    ->where('survey_id', $survey->id)
                    ->where('section_definition_id', $sectionDefinition->id)
                    ->firstOrFail();
            } elseif ($isClone) {
                // New clone - create a new assessment
                $sourceAssessment = SurveySectionAssessment::where('survey_id', $survey->id)
                    ->where('section_definition_id', $sectionDefinition->id)
                    ->where(function($query) {
                        $query->where('is_clone', false)
                              ->where('clone_index', 0)
                              ->orWhereNull('is_clone');
                    })
                    ->orderBy('clone_index')
                    ->orderBy('id')
                    ->first();
                
                if (!$sourceAssessment) {
                    $sourceAssessment = new SurveySectionAssessment();
                    $sourceAssessment->survey_id = $survey->id;
                    $sourceAssessment->section_definition_id = $sectionDefinition->id;
                    $sourceAssessment->is_clone = false;
                    $sourceAssessment->clone_index = 0;
                    $sourceAssessment->save();
                }
                
                $cloneIndex = SurveySectionAssessment::where('survey_id', $survey->id)
                    ->where('section_definition_id', $sectionDefinition->id)
                    ->where('is_clone', true)
                    ->max('clone_index') ?? 0;
                $cloneIndex += 1;
                
                $assessment = new SurveySectionAssessment();
                $assessment->survey_id = $survey->id;
                $assessment->section_definition_id = $sectionDefinition->id;
                $assessment->is_clone = true;
                $assessment->cloned_from_id = $sourceAssessment->id;
                $assessment->clone_index = $cloneIndex;
            } else {
                $assessment = SurveySectionAssessment::where('survey_id', $survey->id)
                    ->where('section_definition_id', $sectionDefinition->id)
                    ->where(function($query) {
                        $query->where('is_clone', false)
                              ->orWhereNull('is_clone');
                    })
                    ->where('clone_index', 0)
                    ->first();
                
                if (!$assessment) {
                    $assessment = new SurveySectionAssessment();
                    $assessment->survey_id = $survey->id;
                    $assessment->section_definition_id = $sectionDefinition->id;
                    $assessment->is_clone = false;
                    $assessment->clone_index = 0;
                }
            }

            // Get option type IDs
            $optionTypes = SurveyOptionType::whereIn('key_name', ['section_type', 'location', 'structure', 'material', 'remaining_life', 'defects'])
                ->get()
                ->keyBy('key_name');

            $sectionDefinition->load('subcategory.category');
            
            // Map form values to option IDs
            $sectionTypeId = $this->findSectionOptionId($optionTypes['section_type']->id ?? null, $formData['section'] ?? null, $sectionDefinition->subcategory_id);
            $locationId = $this->findSectionOptionId($optionTypes['location']->id ?? null, $formData['location'] ?? null);
            $structureId = $this->findSectionOptionId($optionTypes['structure']->id ?? null, $formData['structure'] ?? null, $sectionDefinition->subcategory->category_id ?? null);
            $materialId = $this->findSectionOptionId($optionTypes['material']->id ?? null, $formData['material'] ?? null, $sectionDefinition->subcategory->category_id ?? null);
            $remainingLifeId = $this->findSectionOptionId($optionTypes['remaining_life']->id ?? null, $formData['remaining_life'] ?? null);

            // Update assessment fields
            $assessment->survey_id = $survey->id;
            $assessment->section_definition_id = $sectionDefinition->id;
            $assessment->section_type_id = $sectionTypeId;
            $assessment->location_id = $locationId;
            $assessment->structure_id = $structureId;
            $assessment->material_id = $materialId;
            $assessment->remaining_life_id = $remainingLifeId;
            $assessment->notes = $formData['notes'] ?? null;
            
            $conditionRating = $this->mapConditionRating($formData['condition_rating'] ?? null);
            $assessment->condition_rating = $conditionRating;
            
            $assessment->is_completed = true;
            $assessment->completed_at = now();
            $assessment->save();
            $assessment->refresh();

            // Sync defects
            if (isset($formData['defects']) && is_array($formData['defects'])) {
                $defectOptionIds = $this->findSectionDefectOptionIds($optionTypes['defects']->id ?? null, $formData['defects']);
                $assessment->defects()->sync($defectOptionIds);
            }

            // Save costs
            if (isset($formData['costs']) && is_array($formData['costs'])) {
                $this->saveSectionCosts($assessment, $formData['costs']);
            }

            // Load assessment relationships for ChatGPT
            $assessment->load(['sectionType', 'location', 'structure', 'material', 'remainingLife', 'defects', 'costs']);
            
            $chatGPTData = $this->prepareSectionChatGPTData($assessment, $formData);
            
            $reportContent = '';
            try {
                $categoryName = $sectionDefinition->subcategory->category->display_name ?? 'Unknown';
                $sectionName = $sectionDefinition->display_name;
                $reportContent = $this->chatGPTService->generateReport($chatGPTData, $sectionName, $categoryName);
                
                $assessment->report_content = $reportContent;
                $assessment->save();
            } catch (\Exception $e) {
                Log::error('Failed to generate report content', [
                    'assessment_id' => $assessment->id,
                    'error' => $e->getMessage(),
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'assessment' => $assessment,
                'report_content' => $reportContent,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save section assessment', [
                'survey_id' => $survey->id,
                'section_definition_id' => $sectionDefinition->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Save photos for a section assessment.
     */
    public function saveSectionPhotos(SurveySectionAssessment $assessment, array $photos): void
    {
        if (empty($photos)) {
            return;
        }

        $surveyId = $assessment->survey_id;
        $assessmentId = $assessment->id;
        $storagePath = "survey-photos/{$surveyId}/{$assessmentId}";
        
        $maxSortOrder = \App\Models\SurveySectionPhoto::where('section_assessment_id', $assessmentId)
            ->max('sort_order') ?? -1;
        
        foreach ($photos as $index => $photo) {
            if (!$photo->isValid()) {
                continue;
            }
            
            $extension = $photo->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '_' . $index . '.' . $extension;
            $filePath = $photo->storeAs($storagePath, $filename, 'public');
            
            if ($filePath) {
                \App\Models\SurveySectionPhoto::create([
                    'section_assessment_id' => $assessmentId,
                    'file_path' => $filePath,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }
    }

    /**
     * Find section option ID by value and option type.
     */
    protected function findSectionOptionId(?int $optionTypeId, ?string $value, ?int $scopeId = null): ?int
    {
        if (!$optionTypeId || !$value) {
            return null;
        }

        $query = SurveyOption::where('option_type_id', $optionTypeId)
            ->where('value', $value)
            ->where('is_active', true);

        if ($scopeId) {
            $scopedOption = (clone $query)
                ->where('scope_id', $scopeId)
                ->whereIn('scope_type', ['category', 'subcategory'])
                ->first();
            
            if ($scopedOption) {
                return $scopedOption->id;
            }
        }

        $globalOption = (clone $query)
            ->where('scope_type', 'global')
            ->whereNull('scope_id')
            ->first();

        return $globalOption->id ?? null;
    }

    /**
     * Find section defect option IDs by values.
     */
    protected function findSectionDefectOptionIds(?int $defectOptionTypeId, array $defectValues): array
    {
        if (!$defectOptionTypeId || empty($defectValues)) {
            return [];
        }

        return SurveyOption::where('option_type_id', $defectOptionTypeId)
            ->whereIn('value', $defectValues)
            ->where('is_active', true)
            ->where('scope_type', 'global')
            ->pluck('id')
            ->toArray();
    }

    /**
     * Save costs for section assessment.
     */
    public function saveSectionCosts(SurveySectionAssessment $assessment, array $costs): void
    {
        $assessment->costs()->delete();

        foreach ($costs as $cost) {
            if (empty($cost['category']) || empty($cost['description'])) {
                continue;
            }

            SurveySectionCost::create([
                'section_assessment_id' => $assessment->id,
                'category' => $cost['category'],
                'description' => $cost['description'],
                'due_year' => !empty($cost['due']) ? (int)$cost['due'] : null,
                'amount' => isset($cost['cost']) ? (float)str_replace(['£', ','], '', $cost['cost']) : 0.00,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Prepare data for ChatGPT service (section).
     */
    protected function prepareSectionChatGPTData(SurveySectionAssessment $assessment, array $formData): array
    {
        return [
            'section' => $assessment->sectionType->value ?? $formData['section'] ?? '',
            'location' => $assessment->location->value ?? $formData['location'] ?? '',
            'structure' => $assessment->structure->value ?? $formData['structure'] ?? '',
            'material' => $assessment->material->value ?? $formData['material'] ?? '',
            'defects' => $assessment->defects->pluck('value')->toArray() ?: ($formData['defects'] ?? []),
            'remaining_life' => $assessment->remainingLife->value ?? $formData['remaining_life'] ?? '',
            'notes' => $assessment->notes ?? $formData['notes'] ?? '',
            'costs' => $assessment->costs->map(function($cost) {
                return [
                    'category' => $cost->category,
                    'description' => $cost->description,
                    'due' => $cost->due_year ? (string)$cost->due_year : '',
                    'cost' => number_format($cost->amount, 2),
                ];
            })->toArray() ?: ($formData['costs'] ?? []),
        ];
    }

    /**
     * Map condition rating from string/numeric to integer.
     */
    public function mapConditionRating($rating): ?int
    {
        if ($rating === null || $rating === 'ni' || $rating === '') {
            return null;
        }

        if (is_numeric($rating)) {
            return (int)$rating;
        }

        $mapping = [
            'excellent' => 1,
            'good' => 2,
            'fair' => 3,
            'poor' => 3,
        ];

        return $mapping[strtolower($rating)] ?? null;
    }

}

