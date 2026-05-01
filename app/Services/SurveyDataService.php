<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveySectionAssessment;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use App\Models\SurveySectionDefinition;
use App\Models\SurveyLevel;
use App\Models\SurveyOptionType;
use App\Models\SurveyOption;
use App\Models\SurveySectionCost;
use App\Models\SurveySectionOptionValue;
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

    protected function getRealGroupedData(Survey $survey): array
    {
        // Get section definitions based on survey level
        // If survey has no level set (null/empty), show all sections for backward compatibility
        // If survey has a level set, only show sections assigned to that level
        
        if (empty($survey->level)) {
            // No level set - show all active sections (backward compatibility for old surveys)
            $sectionDefinitions = SurveySectionDefinition::with(['subcategory.category', 'requiredFields'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } else {
            // Level is set - only show sections assigned to this level
            $surveyLevel = $this->findSurveyLevelByValue($survey->level);
            
            if (!$surveyLevel) {
                // Level doesn't exist in database - return empty
                $sectionDefinitions = collect();
            } else {
                // Level exists - get assigned sections
                $sectionDefinitionIds = $surveyLevel->sectionDefinitions()->pluck('survey_section_definitions.id')->unique();
                
                if ($sectionDefinitionIds->isEmpty()) {
                    // Level exists but has no sections assigned - return empty
                    $sectionDefinitions = collect();
                } else {
                    // Level exists and has sections - return only those sections
                    $sectionDefinitions = SurveySectionDefinition::with(['subcategory.category', 'requiredFields'])
                        ->whereIn('id', $sectionDefinitionIds)
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();
                }
            }
        }

        // Filter "Accommodation Components" sections to only those relevant to this survey.
        // (i.e. components configured on accommodation types assigned to the survey's level)
        $relevantAccComponentKeys = $this->getRelevantAccommodationComponentKeysForSurvey($survey);
        if (!empty($relevantAccComponentKeys)) {
            $sectionDefinitions = $sectionDefinitions->filter(function (SurveySectionDefinition $def) use ($relevantAccComponentKeys) {
                $def->loadMissing('subcategory');
                if (($def->subcategory->name ?? '') !== 'accommodation_components') {
                    return true;
                }
                // Section definition name format: acc_component__{component_key}
                $name = (string) $def->name;
                $prefix = 'acc_component__';
                if (strpos($name, $prefix) !== 0) {
                    return false;
                }
                $key = substr($name, strlen($prefix));
                return $key !== '' && in_array($key, $relevantAccComponentKeys, true);
            })->values();
        }
        
        // Load existing assessments for this survey
        $assessments = SurveySectionAssessment::where('survey_id', $survey->id)
            ->with([
                'sectionDefinition.subcategory.category',
                'sectionDefinition.requiredFields',
                'sectionType',
                'location',
                'structure',
                'material',
                'remainingLife',
                'defects',
                'optionValues.option.optionType',
                'photos' => function($query) {
                    $query->orderBy('sort_order');
                },
                'costs',
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
     * Accommodation component keys relevant to this survey (based on level-assigned accommodation types).
     *
     * - If the survey has no level: return [] (do not filter; show all component sections for legacy surveys).
     * - If the level is missing/has no accommodation types: return [] (show none if sections exist, but keep existing behavior elsewhere).
     *
     * @return array<int, string>
     */
    protected function getRelevantAccommodationComponentKeysForSurvey(Survey $survey): array
    {
        if (empty($survey->level)) {
            return [];
        }

        $surveyLevel = $this->findSurveyLevelByValue($survey->level);
        if (!$surveyLevel) {
            return [];
        }

        $typeIds = $surveyLevel->accommodationTypes()->pluck('survey_accommodation_types.id')->unique()->filter();
        if ($typeIds->isEmpty()) {
            return [];
        }

        $keys = DB::table('survey_accommodation_type_components')
            ->join('survey_accommodation_components', 'survey_accommodation_components.id', '=', 'survey_accommodation_type_components.component_id')
            ->whereIn('survey_accommodation_type_components.accommodation_type_id', $typeIds->all())
            ->where('survey_accommodation_components.is_active', true)
            ->pluck('survey_accommodation_components.key_name')
            ->unique()
            ->filter()
            ->values()
            ->all();

        return array_map('strval', $keys);
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
        foreach ($categoriesRaw as &$sections) {
            foreach ($sections as &$section) {
                $section = $this->enrichSectionViewForMock($section);
            }
        }
        unset($sections, $section);

        return $this->groupSectionsBySubCategory($categoriesRaw);
    }

    /**
     * Add enabled_option_fields + option_selections for mock UI (useMockData path).
     *
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected function enrichSectionViewForMock(array $section): array
    {
        $defaults = $this->defaultEnabledOptionTypes();
        $section['enabled_option_fields'] = $this->buildEnabledOptionFieldsMeta($defaults);
        $section['option_selections'] = [
            'section_type' => $section['selected_section'] ?? '',
            'location' => $section['location'] ?? '',
            'structure' => $section['structure'] ?? '',
            'material' => $section['material'] ?? '',
            'defects' => $section['defects'] ?? [],
            'remaining_life' => $section['remaining_life'] ?? '',
        ];

        return $section;
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
        $sectionDef->loadMissing('subcategory.category', 'requiredFields');
        $subcategoryKey = $sectionDef->subcategory->name ?? '';

        $types = $this->getEnabledOptionTypesForSection($sectionDef);
        $selections = $this->buildOptionSelectionsFromAssessment($sectionDef, $assessment);

        $sectionName = $sectionDef->display_name;
        $sectionTypeVal = $selections['section_type'] ?? '';
        if ($sectionTypeVal !== '') {
            $sectionName = $sectionDef->display_name . ' [' . $sectionTypeVal . ']';
        }

        $completionData = $this->calculateCompletionCount($assessment, $types, $selections);

        $defectsList = isset($selections['defects'])
            ? (is_array($selections['defects']) ? $selections['defects'] : array_filter([$selections['defects']]))
            : [];

        return [
            'id' => $assessment ? $assessment->id : 'new_' . $sectionDef->id,
            'section_id' => $sectionDef->id,
            'name' => $sectionName,
            'subcategory_key' => $subcategoryKey,
            'completion' => $completionData['count'],
            'total' => $completionData['total'],
            'condition_rating' => $assessment ? $this->mapConditionRatingFromNumeric($assessment->condition_rating) : 'ni',
            'enabled_option_fields' => $this->buildEnabledOptionFieldsMeta($types),
            'option_selections' => $selections,
            'selected_section' => $sectionTypeVal,
            'location' => $selections['location'] ?? '',
            'structure' => $selections['structure'] ?? '',
            'material' => $selections['material'] ?? '',
            'defects' => $defectsList,
            'remaining_life' => $selections['remaining_life'] ?? '',
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
                // Use storage disk URL so production gets absolute URL from APP_URL
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($photo->file_path);
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
     * Notes, costs, and photos are counted as 1 if they exist, 0 if they don't.
     *
     * @param  array<string, mixed>  $selections  keyed by option type key_name
     * @return array{count: int, total: int}
     */
    protected function calculateCompletionCount(
        ?SurveySectionAssessment $assessment,
        Collection $enabledTypes,
        array $selections = []
    ): array {
        if (!$assessment) {
            $enabledCount = $enabledTypes->count();
            $total = $enabledCount + 1 + 3;

            return ['count' => 0, 'total' => $total];
        }

        $count = 0;

        foreach ($enabledTypes as $ot) {
            $key = $ot->key_name;
            $val = $selections[$key] ?? null;
            if ($ot->is_multiple) {
                if (is_array($val) && count(array_filter($val, fn ($v) => $v !== null && $v !== '')) > 0) {
                    $count++;
                }
            } else {
                if ($val !== null && $val !== '') {
                    $count++;
                }
            }
        }

        if ($assessment->condition_rating !== null) {
            $count++;
        }

        $hasNotes = !empty(trim($assessment->notes ?? ''));
        if ($hasNotes) {
            $count++;
        }

        $hasCosts = $assessment->costs()->count() > 0;
        if ($hasCosts) {
            $count++;
        }

        $hasPhotos = $assessment->photos()->count() > 0;
        if ($hasPhotos) {
            $count++;
        }

        $enabledCount = $enabledTypes->count();
        $total = $enabledCount + 1 + 3;

        return [
            'count' => $count,
            'total' => $total,
        ];
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
     * Default option types when a section has no rows in survey_section_required_fields (backward compatibility).
     */
    public function defaultEnabledOptionTypes(): Collection
    {
        return SurveyOptionType::query()
            ->whereIn('key_name', ['section_type', 'location', 'structure', 'material', 'defects', 'remaining_life'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Enabled option types for a section definition (admin toggles + custom types).
     */
    public function getEnabledOptionTypesForSection(SurveySectionDefinition $sectionDef): Collection
    {
        $sectionDef->loadMissing('requiredFields', 'subcategory');

        // Accommodation Components are special: they behave like "component forms"
        // and should not show the generic "Select section" field.
        if (($sectionDef->subcategory->name ?? '') === 'accommodation_components') {
            return SurveyOptionType::query()
                ->whereIn('key_name', ['material', 'location', 'defects'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        $fields = $sectionDef->requiredFields;
        if ($fields->isEmpty()) {
            return $this->defaultEnabledOptionTypes();
        }

        return $fields;
    }

    /**
     * UI data-group for buttons: section_type uses "section" for legacy JS selectors.
     */
    public function formUiGroupForOptionKey(string $keyName): string
    {
        return $keyName === 'section_type' ? 'section' : $keyName;
    }

    /**
     * Meta for surveyor section-item Blade: enabled fields with labels and UI group keys.
     *
     * @return array<int, array{key_name: string, label: string, is_multiple: bool, data_group: string}>
     */
    public function buildEnabledOptionFieldsMeta(Collection $optionTypes): array
    {
        return $optionTypes->map(function (SurveyOptionType $ot) {
            $key = $ot->key_name;

            return [
                'key_name' => $key,
                'label' => $ot->label,
                'is_multiple' => (bool) $ot->is_multiple,
                'data_group' => $this->formUiGroupForOptionKey($key),
            ];
        })->values()->all();
    }

    /**
     * Scoped option values for any option type key (built-ins delegate to existing helpers).
     */
    public function getOptionsForOptionTypeKey(string $keyName, ?string $categoryName, ?string $subCategoryKey): array
    {
        return match ($keyName) {
            'section_type' => $this->getSectionOptions($categoryName ?? '', $subCategoryKey),
            'location' => $this->getLocationOptions($categoryName, $subCategoryKey),
            'structure' => $this->getStructureOptions($categoryName ?? '', $subCategoryKey),
            'material' => $this->getMaterialOptions($categoryName ?? '', $subCategoryKey),
            'defects' => $this->getDefectOptions($categoryName, $subCategoryKey),
            'remaining_life' => $this->getRemainingLifeOptions($categoryName, $subCategoryKey),
            default => $this->getGenericOptionsForTypeKey($keyName, $categoryName, $subCategoryKey),
        };
    }

    /**
     * Options for a custom option type (same scoping rules as material).
     */
    protected function getGenericOptionsForTypeKey(string $keyName, ?string $categoryName, ?string $subCategoryKey): array
    {
        $type = SurveyOptionType::where('key_name', $keyName)->where('is_active', true)->first();
        if (!$type) {
            return [];
        }

        if ($categoryName === null || $categoryName === '') {
            return SurveyOption::where('option_type_id', $type->id)
                ->where('is_active', true)
                ->where('scope_type', 'global')
                ->whereNull('scope_id')
                ->orderBy('sort_order')
                ->pluck('value')
                ->toArray();
        }

        $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
        if (!$category) {
            return SurveyOption::where('option_type_id', $type->id)
                ->where('is_active', true)
                ->where('scope_type', 'global')
                ->whereNull('scope_id')
                ->orderBy('sort_order')
                ->pluck('value')
                ->toArray();
        }

        $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
            ->pluck('id')
            ->toArray();

        return SurveyOption::where('option_type_id', $type->id)
            ->where('is_active', true)
            ->where(function ($query) use ($category, $subcategoryIds, $subCategoryKey) {
                $query->where('scope_type', 'global')
                    ->orWhere(function ($q) use ($category) {
                        $q->where('scope_type', 'category')
                            ->where('scope_id', $category->id);
                    })
                    ->orWhere(function ($q) use ($subcategoryIds, $subCategoryKey) {
                        $q->where('scope_type', 'subcategory');
                        if ($subCategoryKey) {
                            $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                            if ($subcategory) {
                                $q->where('scope_id', $subcategory->id);
                            } else {
                                $q->whereIn('scope_id', $subcategoryIds);
                            }
                        } else {
                            $q->whereIn('scope_id', $subcategoryIds);
                        }
                    });
            })
            ->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Resolve a survey_options.id for a value with subcategory > category > global precedence.
     */
    public function resolveSurveyOptionId(int $optionTypeId, string $value, ?int $categoryId, ?int $subcategoryId): ?int
    {
        if ($value === '') {
            return null;
        }

        $base = SurveyOption::query()
            ->where('option_type_id', $optionTypeId)
            ->where('value', $value)
            ->where('is_active', true);

        if ($subcategoryId) {
            $row = (clone $base)->where('scope_type', 'subcategory')->where('scope_id', $subcategoryId)->first();
            if ($row) {
                return $row->id;
            }
        }

        if ($categoryId) {
            $row = (clone $base)->where('scope_type', 'category')->where('scope_id', $categoryId)->first();
            if ($row) {
                return $row->id;
            }
        }

        $row = (clone $base)->where('scope_type', 'global')->whereNull('scope_id')->first();

        return $row?->id;
    }

    /**
     * Merge request options with legacy flat keys (section, material, ...).
     *
     * @return array<string, mixed>
     */
    public function normalizeOptionsFromFormData(array $formData): array
    {
        $options = isset($formData['options']) && is_array($formData['options']) ? $formData['options'] : [];

        if (isset($formData['section']) && !array_key_exists('section_type', $options)) {
            $options['section_type'] = $formData['section'];
        }
        if (isset($formData['location']) && !array_key_exists('location', $options)) {
            $options['location'] = $formData['location'];
        }
        if (isset($formData['structure']) && !array_key_exists('structure', $options)) {
            $options['structure'] = $formData['structure'];
        }
        if (isset($formData['material']) && !array_key_exists('material', $options)) {
            $options['material'] = $formData['material'];
        }
        if (isset($formData['remaining_life']) && !array_key_exists('remaining_life', $options)) {
            $options['remaining_life'] = $formData['remaining_life'];
        }
        if (isset($formData['defects']) && is_array($formData['defects']) && !array_key_exists('defects', $options)) {
            $options['defects'] = $formData['defects'];
        }

        return $options;
    }

    /**
     * Build selections keyed by option type key_name from generic rows + legacy FKs.
     *
     * @return array<string, mixed>
     */
    protected function buildOptionSelectionsFromAssessment(
        SurveySectionDefinition $sectionDef,
        ?SurveySectionAssessment $assessment
    ): array {
        $types = $this->getEnabledOptionTypesForSection($sectionDef);
        $selections = [];

        if ($assessment) {
            $assessment->loadMissing(['optionValues.option.optionType', 'sectionType', 'location', 'structure', 'material', 'remainingLife', 'defects']);

            $byTypeId = [];
            foreach ($assessment->optionValues as $ov) {
                if (!$ov->option || !$ov->optionType) {
                    continue;
                }
                $tid = $ov->option_type_id;
                if (!isset($byTypeId[$tid])) {
                    $byTypeId[$tid] = [];
                }
                $byTypeId[$tid][] = $ov->option->value;
            }

            foreach ($types as $ot) {
                $key = $ot->key_name;
                $vals = $byTypeId[$ot->id] ?? null;

                if (!empty($vals)) {
                    $selections[$key] = $ot->is_multiple ? $vals : ($vals[0] ?? '');
                    continue;
                }

                $selections[$key] = $this->legacySelectionForOptionKey($assessment, $key, $ot->is_multiple);
            }
        } else {
            foreach ($types as $ot) {
                $selections[$ot->key_name] = $ot->is_multiple ? [] : '';
            }
        }

        return $selections;
    }

    /**
     * @return array|mixed|string
     */
    protected function legacySelectionForOptionKey(SurveySectionAssessment $assessment, string $keyName, bool $isMultiple)
    {
        return match ($keyName) {
            'section_type' => $assessment->sectionType->value ?? '',
            'location' => $assessment->location->value ?? '',
            'structure' => $assessment->structure->value ?? '',
            'material' => $assessment->material->value ?? '',
            'remaining_life' => $assessment->remainingLife->value ?? '',
            'defects' => $assessment->defects->pluck('value')->toArray(),
            default => $isMultiple ? [] : '',
        };
    }

    /**
     * Persist generic option selections and dual-write legacy columns for built-in types.
     *
     * @param  array<string, mixed>  $options  keyed by option type key_name
     */
    protected function syncSectionOptionValues(
        SurveySectionAssessment $assessment,
        SurveySectionDefinition $sectionDef,
        array $options
    ): void {
        $sectionDef->loadMissing('subcategory.category');
        $types = $this->getEnabledOptionTypesForSection($sectionDef);
        $categoryId = $sectionDef->subcategory->category_id ?? null;
        $subcategoryId = $sectionDef->subcategory_id;

        SurveySectionOptionValue::where('section_assessment_id', $assessment->id)->delete();

        $sectionTypeId = null;
        $locationId = null;
        $structureId = null;
        $materialId = null;
        $remainingLifeId = null;
        $defectIds = [];

        foreach ($types as $ot) {
            $key = $ot->key_name;
            $raw = $options[$key] ?? null;
            if ($ot->is_multiple) {
                $values = is_array($raw) ? $raw : array_filter([$raw]);
            } else {
                $values = $raw !== null && $raw !== '' ? [is_array($raw) ? reset($raw) : $raw] : [];
                $values = array_slice($values, 0, 1);
            }

            foreach ($values as $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                $optionId = $this->resolveSurveyOptionId($ot->id, (string) $value, $categoryId, $subcategoryId);
                if (!$optionId) {
                    continue;
                }
                SurveySectionOptionValue::create([
                    'section_assessment_id' => $assessment->id,
                    'option_type_id' => $ot->id,
                    'option_id' => $optionId,
                ]);

                if ($key === 'section_type') {
                    $sectionTypeId = $sectionTypeId ?? $optionId;
                }
                if ($key === 'location') {
                    $locationId = $locationId ?? $optionId;
                }
                if ($key === 'structure') {
                    $structureId = $structureId ?? $optionId;
                }
                if ($key === 'material') {
                    $materialId = $materialId ?? $optionId;
                }
                if ($key === 'remaining_life') {
                    $remainingLifeId = $remainingLifeId ?? $optionId;
                }
                if ($key === 'defects') {
                    $defectIds[] = $optionId;
                }
            }
        }

        $assessment->section_type_id = $types->contains(fn ($t) => $t->key_name === 'section_type') ? $sectionTypeId : null;
        $assessment->location_id = $types->contains(fn ($t) => $t->key_name === 'location') ? $locationId : null;
        $assessment->structure_id = $types->contains(fn ($t) => $t->key_name === 'structure') ? $structureId : null;
        $assessment->material_id = $types->contains(fn ($t) => $t->key_name === 'material') ? $materialId : null;
        $assessment->remaining_life_id = $types->contains(fn ($t) => $t->key_name === 'remaining_life') ? $remainingLifeId : null;
        $assessment->save();

        if ($types->contains(fn ($t) => $t->key_name === 'defects')) {
            $assessment->defects()->sync(array_unique($defectIds));
        } else {
            $assessment->defects()->sync([]);
        }
    }

    /**
     * Get all options mapping in a structured format from database.
     * 
     * @return array
     */
    public function getOptionsMapping(): array
    {
        $mapping = [
            // Global options (for backward compatibility)
            'location' => $this->getLocationOptions(),
            'remaining_life' => $this->getRemainingLifeOptions(),
            'defects' => $this->getDefectOptions(),
        ];

        // Get category-based options (all option types with merged scopes)
        $categories = SurveyCategory::with(['subcategories' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $category) {
            $categoryName = $category->display_name;

            // Base (category-scoped) options
            $mapping[$categoryName] = [
                'section' => $this->getSectionOptions($categoryName),
                'location' => $this->getLocationOptions($categoryName),
                'structure' => $this->getStructureOptions($categoryName),
                'material' => $this->getMaterialOptions($categoryName),
                'defects' => $this->getDefectOptions($categoryName),
                'remaining_life' => $this->getRemainingLifeOptions($categoryName),
                // Sub-category specific options (global + category + that subcategory only)
                'by_subcategory' => [],
            ];

            foreach ($category->subcategories as $subcategory) {
                $subCategoryKey = $subcategory->name;

                $mapping[$categoryName]['by_subcategory'][$subCategoryKey] = [
                    'section' => $this->getSectionOptions($categoryName, $subCategoryKey),
                    'location' => $this->getLocationOptions($categoryName, $subCategoryKey),
                    'structure' => $this->getStructureOptions($categoryName, $subCategoryKey),
                    'material' => $this->getMaterialOptions($categoryName, $subCategoryKey),
                    'defects' => $this->getDefectOptions($categoryName, $subCategoryKey),
                    'remaining_life' => $this->getRemainingLifeOptions($categoryName, $subCategoryKey),
                ];
            }
        }

        return $mapping;
    }

    /**
     * Get section options based on category and subcategory from database (global + category + subcategory scoped).
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

        // Get subcategory IDs for this category
        $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
            ->pluck('id')
            ->toArray();

        $query = SurveyOption::where('option_type_id', $sectionType->id)
            ->where('is_active', true);

        // Include global + category + subcategory scoped options
        $query->where(function ($q) use ($category, $subcategoryIds, $subCategoryKey) {
            $q->where('scope_type', 'global')
              ->orWhere(function ($inner) use ($category) {
                  $inner->where('scope_type', 'category')
                        ->where('scope_id', $category->id);
              })
              ->orWhere(function ($inner) use ($subcategoryIds, $subCategoryKey) {
                  $inner->where('scope_type', 'subcategory');
                  if ($subCategoryKey) {
                      // If specific subcategory requested, filter to that one
                      $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                      if ($subcategory) {
                          $inner->where('scope_id', $subcategory->id);
                      }
                  } else {
                      // Otherwise include all subcategories for this category
                      $inner->whereIn('scope_id', $subcategoryIds);
                  }
              });
        });

        return $query->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Get location options from database (global + category + subcategory scoped).
     * 
     * @param string|null $categoryName
     * @param string|null $subCategoryKey
     * @return array
     */
    public function getLocationOptions(?string $categoryName = null, ?string $subCategoryKey = null): array
    {
        $locationType = SurveyOptionType::where('key_name', 'location')->first();
        if (!$locationType) {
            return [];
        }

        $query = SurveyOption::where('option_type_id', $locationType->id)
            ->where('is_active', true);

        if ($categoryName) {
            $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
            
            if ($category) {
                // Get subcategory IDs for this category
                $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                // Include global + category + subcategory scoped options
                $query->where(function ($q) use ($category, $subcategoryIds, $subCategoryKey) {
                    $q->where('scope_type', 'global')
                      ->orWhere(function ($inner) use ($category) {
                          $inner->where('scope_type', 'category')
                                ->where('scope_id', $category->id);
                      })
                      ->orWhere(function ($inner) use ($subcategoryIds, $subCategoryKey) {
                          $inner->where('scope_type', 'subcategory');

                          if ($subCategoryKey) {
                              // If specific subcategory requested, filter to that one
                              $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                              if ($subcategory) {
                                  $inner->where('scope_id', $subcategory->id);
                              } else {
                                  // Fallback: include all subcategories for this category
                                  $inner->whereIn('scope_id', $subcategoryIds);
                              }
                          } else {
                              // Otherwise include all subcategories for this category
                              $inner->whereIn('scope_id', $subcategoryIds);
                          }
                      });
                });
            } else {
                // Category not found, return only global
                $query->where('scope_type', 'global');
            }
        } else {
            // No category specified, return only global
            $query->where('scope_type', 'global');
        }

        return $query->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
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

        // Get subcategory IDs for this category
        $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
            ->pluck('id')
            ->toArray();

        // Get global + category + subcategory scoped options
        return SurveyOption::where('option_type_id', $structureType->id)
            ->where('is_active', true)
            ->where(function ($query) use ($category, $subcategoryIds, $subCategoryKey) {
                $query->where('scope_type', 'global')
                      ->orWhere(function ($q) use ($category) {
                          $q->where('scope_type', 'category')
                            ->where('scope_id', $category->id);
                      })
                      ->orWhere(function ($q) use ($subcategoryIds, $subCategoryKey) {
                          $q->where('scope_type', 'subcategory');

                          if ($subCategoryKey) {
                              // If specific subcategory requested, filter to that one
                              $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                              if ($subcategory) {
                                  $q->where('scope_id', $subcategory->id);
                              } else {
                                  // Fallback: include all subcategories for this category
                                  $q->whereIn('scope_id', $subcategoryIds);
                              }
                          } else {
                              // Otherwise include all subcategories for this category
                              $q->whereIn('scope_id', $subcategoryIds);
                          }
                      });
            })
            ->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
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

        // Get subcategory IDs for this category
        $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
            ->pluck('id')
            ->toArray();

        // Get global + category + subcategory scoped options
        return SurveyOption::where('option_type_id', $materialType->id)
            ->where('is_active', true)
            ->where(function ($query) use ($category, $subcategoryIds, $subCategoryKey) {
                $query->where('scope_type', 'global')
                      ->orWhere(function ($q) use ($category) {
                          $q->where('scope_type', 'category')
                            ->where('scope_id', $category->id);
                      })
                      ->orWhere(function ($q) use ($subcategoryIds, $subCategoryKey) {
                          $q->where('scope_type', 'subcategory');

                          if ($subCategoryKey) {
                              // If specific subcategory requested, filter to that one
                              $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                              if ($subcategory) {
                                  $q->where('scope_id', $subcategory->id);
                              } else {
                                  // Fallback: include all subcategories for this category
                                  $q->whereIn('scope_id', $subcategoryIds);
                              }
                          } else {
                              // Otherwise include all subcategories for this category
                              $q->whereIn('scope_id', $subcategoryIds);
                          }
                      });
            })
            ->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray() ?: ['Mixed'];
    }

    /**
     * Get defect options from database (global + category + subcategory scoped).
     * 
     * @param string|null $categoryName
     * @return array
     */
    public function getDefectOptions(?string $categoryName = null, ?string $subCategoryKey = null): array
    {
        $defectType = SurveyOptionType::where('key_name', 'defects')->first();
        if (!$defectType) {
            return [];
        }

        $query = SurveyOption::where('option_type_id', $defectType->id)
            ->where('is_active', true);

        if ($categoryName) {
            $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
            
            if ($category) {
                // Get subcategory IDs for this category
                $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                // Include global + category + subcategory scoped options
                $query->where(function ($q) use ($category, $subcategoryIds, $subCategoryKey) {
                    $q->where('scope_type', 'global')
                      ->orWhere(function ($inner) use ($category) {
                          $inner->where('scope_type', 'category')
                                ->where('scope_id', $category->id);
                      })
                      ->orWhere(function ($inner) use ($subcategoryIds, $subCategoryKey) {
                          $inner->where('scope_type', 'subcategory');

                          if ($subCategoryKey) {
                              // If specific subcategory requested, filter to that one
                              $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                              if ($subcategory) {
                                  $inner->where('scope_id', $subcategory->id);
                              } else {
                                  // Fallback: include all subcategories for this category
                                  $inner->whereIn('scope_id', $subcategoryIds);
                              }
                          } else {
                              // Otherwise include all subcategories for this category
                              $inner->whereIn('scope_id', $subcategoryIds);
                          }
                      });
                });
            } else {
                // Category not found, return only global
                $query->where('scope_type', 'global');
            }
        } else {
            // No category specified, return only global
            $query->where('scope_type', 'global');
        }

        return $query->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Get remaining life options from database (global + category + subcategory scoped).
     * 
     * @param string|null $categoryName
     * @return array
     */
    public function getRemainingLifeOptions(?string $categoryName = null, ?string $subCategoryKey = null): array
    {
        $remainingLifeType = SurveyOptionType::where('key_name', 'remaining_life')->first();
        if (!$remainingLifeType) {
            return [];
        }

        $query = SurveyOption::where('option_type_id', $remainingLifeType->id)
            ->where('is_active', true);

        if ($categoryName) {
            $category = SurveyCategory::where('display_name', $categoryName)->orWhere('name', $categoryName)->first();
            
            if ($category) {
                // Get subcategory IDs for this category
                $subcategoryIds = SurveySubcategory::where('category_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                // Include global + category + subcategory scoped options
                $query->where(function ($q) use ($category, $subcategoryIds, $subCategoryKey) {
                    $q->where('scope_type', 'global')
                      ->orWhere(function ($inner) use ($category) {
                          $inner->where('scope_type', 'category')
                                ->where('scope_id', $category->id);
                      })
                      ->orWhere(function ($inner) use ($subcategoryIds, $subCategoryKey) {
                          $inner->where('scope_type', 'subcategory');

                          if ($subCategoryKey) {
                              // If specific subcategory requested, filter to that one
                              $subcategory = SurveySubcategory::where('name', $subCategoryKey)->first();
                              if ($subcategory) {
                                  $inner->where('scope_id', $subcategory->id);
                              } else {
                                  // Fallback: include all subcategories for this category
                                  $inner->whereIn('scope_id', $subcategoryIds);
                              }
                          } else {
                              // Otherwise include all subcategories for this category
                              $inner->whereIn('scope_id', $subcategoryIds);
                          }
                      });
                });
            } else {
                // Category not found, return only global
                $query->where('scope_type', 'global');
            }
        } else {
            // No category specified, return only global
            $query->where('scope_type', 'global');
        }

        return $query->orderByRaw("FIELD(scope_type, 'global', 'category', 'subcategory')")
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

            $sectionDefinition->load('subcategory.category', 'requiredFields');

            $normalizedOptions = $this->normalizeOptionsFromFormData($formData);

            // Update assessment fields
            $assessment->survey_id = $survey->id;
            $assessment->section_definition_id = $sectionDefinition->id;
            $assessment->notes = $formData['notes'] ?? null;

            $conditionRating = $this->mapConditionRating($formData['condition_rating'] ?? null);
            $assessment->condition_rating = $conditionRating;

            $assessment->is_completed = true;
            $assessment->completed_at = now();
            $assessment->save();
            $assessment->refresh();

            $this->syncSectionOptionValues($assessment, $sectionDefinition, $normalizedOptions);
            $assessment->refresh();

            // Save costs
            if (isset($formData['costs']) && is_array($formData['costs'])) {
                $this->saveSectionCosts($assessment, $formData['costs']);
            }

            // Load assessment relationships for ChatGPT
            $assessment->load(['sectionType', 'location', 'structure', 'material', 'remainingLife', 'defects', 'optionValues.option.optionType', 'costs']);
            
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
        $assessment->loadMissing('optionValues.option.optionType');

        $extras = [];
        foreach ($assessment->optionValues as $ov) {
            if (!$ov->optionType || !$ov->option) {
                continue;
            }
            $k = $ov->optionType->key_name;
            if (in_array($k, ['section_type', 'location', 'structure', 'material', 'defects', 'remaining_life'], true)) {
                continue;
            }
            if ($ov->optionType->is_multiple) {
                $extras[$k] = $extras[$k] ?? [];
                $extras[$k][] = $ov->option->value;
            } else {
                $extras[$k] = $ov->option->value;
            }
        }

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
            'extra_option_selections' => $extras,
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

