<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyLevel;
use App\Models\SurveyAccommodationComponent;
use App\Models\SurveyAccommodationOptionType;
use App\Models\SurveyAccommodationOption;
use App\Models\SurveyAccommodationAssessment;
use App\Models\SurveyAccommodationType;
use App\Models\SurveyAccommodationComponentAssessment;
use App\Models\SurveyAccommodationComponentSummary;
use App\Models\SurveyAccommodationGptOutput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SurveyAccommodationDataService
{
    protected ChatGPTService $chatGPTService;

    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }

    /**
     * Get accommodation configuration data with carousel structure.
     * Uses separate JSON structure for accommodation sections.
     * 
     * @param Survey $survey
     * @param bool $useMockData Whether to use mock data or real database data
     * @return array
     */
    public function getAccommodationConfigurationData(Survey $survey, bool $useMockData = true): array
    {
        // First check if there are any accommodation types with components configured in admin panel
        // If not, return empty array so the section doesn't appear at all
        if (!$this->hasConfiguredAccommodationTypes()) {
            return [];
        }
        
        if ($useMockData) {
            return $this->getMockAccommodationData($survey);
        }
        
        return $this->getRealAccommodationData($survey);
    }

    /**
     * Check if there are any accommodation types with components configured in the admin panel.
     * This determines whether the accommodation section should appear on the survey data page.
     * 
     * @return bool
     */
    protected function hasConfiguredAccommodationTypes(): bool
    {
        $configuredTypes = SurveyAccommodationType::where('is_active', true)
            ->with(['components' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->filter(function($type) {
                return $type->components && $type->components->count() > 0;
            });
        
        Log::info('Checking configured accommodation types', [
            'total_types' => SurveyAccommodationType::where('is_active', true)->count(),
            'types_with_components' => $configuredTypes->count(),
            'types_detail' => $configuredTypes->map(function($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->display_name,
                    'components_count' => $type->components->count(),
                ];
            })->toArray(),
        ]);
        
        return $configuredTypes->count() > 0;
    }

    /**
     * Get mock accommodation data for UI development.
     * Only returns data if there's at least one accommodation type with components configured.
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getMockAccommodationData(Survey $survey): array
    {
        // Find the first accommodation type with components configured (not just bedroom)
        $configuredType = SurveyAccommodationType::where('is_active', true)
            ->with(['components' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->first(function($type) {
                return $type->components && $type->components->count() > 0;
            });
        
        // If a configured type exists, return mock data for it
        if ($configuredType) {
            $accommodationTypeId = $configuredType->id;
            $accommodationTypeName = $configuredType->display_name;
            
            return [
                [
                    'id' => 'accommodation_1',
                    'name' => $accommodationTypeName . ' 1',
                    'display_label' => $accommodationTypeName . ' 1',
                    'clone_index' => 0,
                    'accommodation_type_id' => $accommodationTypeId,
                    'accommodation_type_name' => $accommodationTypeName,
                    'notes' => '', // Shared notes for all components
                    'photos' => [], // Shared photos for all components
                    'location' => '',
                    'completed_components' => 0,
                    'total_components' => $configuredType->components->count(),
                    'components' => $configuredType->components->map(function($component) {
                        return [
                            'component_id' => $component->id,
                            'component_key' => $component->key_name,
                            'component_name' => $component->display_name,
                            'material' => '',
                            'defects' => [],
                            'location' => '',
                            'gpt_observations' => [],
                        ];
                    })->toArray(),
                    'gpt_narrative' => null,
                    'gpt_observations' => [],
                    'form_submitted' => false,
                ],
            ];
        }
        
        // Return empty array if no types with components configured
        return [];
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
     * Accommodation types assigned to the given survey level that count toward property summary.
     * Does not require components to be configured on the type.
     */
    public function getPropertyCountTypesForLevel(?string $levelValue): Collection
    {
        if ($levelValue === null || $levelValue === '') {
            return collect();
        }

        $surveyLevel = $this->findSurveyLevelByValue($levelValue);
        if (!$surveyLevel) {
            return collect();
        }

        $typeIds = $surveyLevel->accommodationTypes()->pluck('survey_accommodation_types.id')->unique()->filter();

        if ($typeIds->isEmpty()) {
            return collect();
        }

        return SurveyAccommodationType::query()
            ->whereIn('id', $typeIds)
            ->where('is_active', true)
            ->where('counts_toward_property', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Resolved counts (type id => int) from JSON with legacy column fallback for flagged types.
     *
     * @return array<int, int>
     */
    public function getResolvedPropertyAccommodationCounts(Survey $survey): array
    {
        $types = $this->getPropertyCountTypesForLevel($survey->level ?? '');
        $raw = $survey->property_accommodation_counts;
        $stored = is_array($raw) ? $raw : [];

        $out = [];
        foreach ($types as $type) {
            $id = $type->id;
            $keyStr = (string) $id;
            $val = null;
            if (array_key_exists($keyStr, $stored)) {
                $val = $stored[$keyStr];
            } elseif (array_key_exists($id, $stored)) {
                $val = $stored[$id];
            }

            if ($val !== null && $val !== '') {
                $out[$id] = max(0, (int) $val);
                continue;
            }

            if ($type->key_name === 'bedroom') {
                $out[$id] = (int) ($survey->number_of_bedrooms ?? 0);
            } elseif ($type->key_name === 'bathroom') {
                $out[$id] = (int) ($survey->bathrooms ?? 0);
            } elseif (in_array($type->key_name, ['living_room', 'reception', 'receptions'], true)) {
                $out[$id] = (int) ($survey->receptions ?? 0);
            } else {
                $out[$id] = 0;
            }
        }

        return $out;
    }

    /**
     * Ensure accommodation assessments exist up to a desired count for a given accommodation type.
     *
     * Used when the survey "Property Type" count is increased (e.g. Bedrooms = 3),
     * so the Survey Data page can show Bedroom 1..3 rows.
     *
     * Non-destructive: if desiredCount is smaller than existing, nothing is deleted.
     */
    public function ensureAccommodationAssessmentsForTypeCount(Survey $survey, int $accommodationTypeId, int $desiredCount): void
    {
        $desiredCount = max(0, (int) $desiredCount);

        $type = SurveyAccommodationType::query()
            ->where('id', $accommodationTypeId)
            ->where('is_active', true)
            ->with(['components' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('survey_accommodation_components.sort_order');
            }])
            ->first();

        if (! $type) {
            return;
        }

        DB::transaction(function () use ($survey, $type, $desiredCount) {
            $existing = SurveyAccommodationAssessment::query()
                ->where('survey_id', $survey->id)
                ->where('accommodation_type_id', $type->id)
                ->orderBy('clone_index')
                ->lockForUpdate()
                ->get();

            // If decreased to 0, delete all assessments for this type.
            // If decreased (e.g. 3 -> 2), delete from the end (highest clone_index first).
            $currentCount = $existing->count();
            if ($desiredCount < $currentCount) {
                // Determine which clone_index values should remain.
                // desiredCount=1 keeps clone_index 0 only. desiredCount=2 keeps 0 and 1, etc.
                $maxKeepCloneIndex = $desiredCount - 1;

                $toDelete = $existing->filter(function ($a) use ($maxKeepCloneIndex, $desiredCount) {
                    if ($desiredCount === 0) {
                        return true;
                    }
                    return (int) ($a->clone_index ?? 0) > $maxKeepCloneIndex;
                })->sortByDesc('clone_index')->values();

                if ($toDelete->isNotEmpty()) {
                    $assessmentIds = $toDelete->pluck('id')->filter()->values()->all();

                    // Delete photos (and files) first
                    $photos = \App\Models\SurveyAccommodationPhoto::whereIn('accommodation_assessment_id', $assessmentIds)->get();
                    foreach ($photos as $p) {
                        $path = (string) ($p->file_path ?? '');
                        if ($path !== '') {
                            try {
                                Storage::disk('public')->delete($path);
                            } catch (\Throwable $e) {
                                // ignore file delete errors; still remove DB rows
                            }
                        }
                    }
                    \App\Models\SurveyAccommodationPhoto::whereIn('accommodation_assessment_id', $assessmentIds)->delete();

                    // Delete component assessments + their pivot defects rows
                    $componentAssessmentIds = \App\Models\SurveyAccommodationComponentAssessment::whereIn('accommodation_assessment_id', $assessmentIds)
                        ->pluck('id')
                        ->all();
                    if (!empty($componentAssessmentIds)) {
                        DB::table('survey_accommodation_component_defects')
                            ->whereIn('component_assessment_id', $componentAssessmentIds)
                            ->delete();
                        \App\Models\SurveyAccommodationComponentAssessment::whereIn('id', $componentAssessmentIds)->delete();
                    }

                    // Finally delete the accommodation assessments
                    \App\Models\SurveyAccommodationAssessment::whereIn('id', $assessmentIds)->delete();

                    // Refresh existing after deletes
                    $existing = SurveyAccommodationAssessment::query()
                        ->where('survey_id', $survey->id)
                        ->where('accommodation_type_id', $type->id)
                        ->orderBy('clone_index')
                        ->lockForUpdate()
                        ->get();
                    $currentCount = $existing->count();
                }
            }

            if ($desiredCount === 0) {
                return;
            }

            // Ensure original (clone_index 0) exists if desiredCount >= 1
            $hasOriginal = $existing->firstWhere('clone_index', 0) !== null;
            if (! $hasOriginal) {
                $orig = new SurveyAccommodationAssessment();
                $orig->survey_id = $survey->id;
                $orig->accommodation_type_id = $type->id;
                $orig->clone_index = 0;
                $orig->custom_name = $type->display_name;
                $orig->is_completed = false;
                $orig->completed_at = null;
                $orig->save();

                // Create empty component rows so the UI renders consistently
                $this->initializeComponentsFromType($orig, $type);

                $existing = $existing->push($orig);
            }

            $currentCount = $existing->count();
            if ($currentCount >= $desiredCount) {
                return;
            }

            $maxCloneIndex = (int) ($existing->max('clone_index') ?? 0);

            while ($currentCount < $desiredCount) {
                $maxCloneIndex++;

                $assessment = new SurveyAccommodationAssessment();
                $assessment->survey_id = $survey->id;
                $assessment->accommodation_type_id = $type->id;
                $assessment->clone_index = $maxCloneIndex;
                $assessment->custom_name = $type->display_name;
                $assessment->is_completed = false;
                $assessment->completed_at = null;
                $assessment->save();

                $this->initializeComponentsFromType($assessment, $type);

                $currentCount++;
            }
        });
    }

    /**
     * Update legacy number_of_bedrooms / receptions / bathrooms from accommodation count map.
     *
     * @param  array<int|string, int>  $countsByTypeId
     */
    public function syncLegacyPropertyCountColumns(Survey $survey, array $countsByTypeId): void
    {
        if ($countsByTypeId === []) {
            return;
        }

        $ids = array_map('intval', array_keys($countsByTypeId));
        $types = SurveyAccommodationType::whereIn('id', $ids)->get()->keyBy('id');

        $updates = [];
        foreach ($countsByTypeId as $tid => $val) {
            $type = $types->get((int) $tid);
            if (!$type) {
                continue;
            }
            $n = max(0, (int) $val);
            if ($type->key_name === 'bedroom') {
                $updates['number_of_bedrooms'] = $n;
            }
            if ($type->key_name === 'bathroom') {
                $updates['bathrooms'] = (string) $n;
            }
            if (in_array($type->key_name, ['living_room', 'reception', 'receptions'], true)) {
                $updates['receptions'] = (string) $n;
            }
        }

        if ($updates !== []) {
            $survey->update($updates);
        }
    }

    protected function getRealAccommodationData(Survey $survey): array
    {
        // Get accommodation types based on survey level
        // If survey has no level set (null/empty), show all types for backward compatibility
        // If survey has a level set, only show types assigned to that level
        
        if (empty($survey->level)) {
            // No level set - show all active types with components (backward compatibility for old surveys)
            $configuredTypes = SurveyAccommodationType::where('is_active', true)
                ->with(['components' => function($query) {
                    $query->where('is_active', true)
                          // Use global Components ordering so the surveyor view
                          // matches the admin Components list (Walls, then Ceiling, etc.)
                          ->orderBy('survey_accommodation_components.sort_order');
                }])
                ->orderBy('sort_order')
                ->get()
                ->filter(function($type) {
                    return $type->components && $type->components->count() > 0;
                });
        } else {
            // Level is set - only show types assigned to this level
            $surveyLevel = $this->findSurveyLevelByValue($survey->level);
            
            if (!$surveyLevel) {
                // Level doesn't exist in database - return empty
                $configuredTypes = collect();
            } else {
                // Level exists - get assigned accommodation types
                $accommodationTypeIds = $surveyLevel->accommodationTypes()->pluck('survey_accommodation_types.id')->unique();
                
                if ($accommodationTypeIds->isEmpty()) {
                    // Level exists but has no accommodation types assigned - return empty
                    $configuredTypes = collect();
                } else {
                    // Level exists and has accommodation types - return only those types
                    $configuredTypes = SurveyAccommodationType::whereIn('id', $accommodationTypeIds)
                        ->where('is_active', true)
                        ->with(['components' => function($query) {
                            $query->where('is_active', true)
                                  // Use global Components ordering so the surveyor view
                                  // matches the admin Components list (Walls, then Ceiling, etc.)
                                  ->orderBy('survey_accommodation_components.sort_order');
                        }])
                        ->orderBy('sort_order')
                        ->get()
                        ->filter(function($type) {
                            return $type->components && $type->components->count() > 0;
                        });
                }
            }
        }
        
        if ($configuredTypes->isEmpty()) {
            return [];
        }
        
        // Get existing assessments for this survey
        $accommodationAssessments = SurveyAccommodationAssessment::where('survey_id', $survey->id)
            ->with([
                'accommodationType' => function($query) {
                    $query->with(['components' => function($q) {
                        $q->where('is_active', true);
                    }]);
                },
                'componentAssessments.component',
                'componentAssessments.material',
                'componentAssessments.defects',
                'componentAssessments.location',
                'location',
                'photos'
            ])
            ->orderBy('clone_index')
            ->get();
        
        // Filter assessments to only include those where the accommodation type has components configured
        $validAssessments = $accommodationAssessments->filter(function($assessment) {
            // Exclude assessments without accommodation_type_id
            if (!$assessment->accommodation_type_id) {
                return false;
            }
            
            // Exclude if accommodation type relationship is missing
            if (!$assessment->accommodationType) {
                return false;
            }
            
            // Ensure components relationship is loaded with active filter
            if (!$assessment->accommodationType->relationLoaded('components')) {
                $assessment->accommodationType->load(['components' => function($query) {
                    $query->where('is_active', true);
                }]);
            }
            
            // Only include if the type has active components configured in admin panel
            if (!$assessment->accommodationType->components || $assessment->accommodationType->components->count() === 0) {
                return false;
            }
            
            return true;
        });
        
        // Group assessments by accommodation type ID
        $assessmentsByTypeId = $validAssessments->groupBy('accommodation_type_id');

        $gptByType = SurveyAccommodationGptOutput::query()
            ->where('survey_id', $survey->id)
            ->get()
            ->keyBy('accommodation_type_id');

        // Build result array: for each configured type, use existing assessment or create default
        $result = [];
        foreach ($configuredTypes as $type) {
            $typeId = $type->id;
            
            // Check if there's an existing assessment for this type (clone_index = 0 is the original)
            $existingAssessment = $assessmentsByTypeId->get($typeId)
                ?->first(function($assessment) {
                    return $assessment->clone_index == 0;
                });
            
            if ($existingAssessment) {
                // Use existing assessment
                $result[] = $this->mapAssessmentToArray($existingAssessment, $gptByType);
            } else {
                $gptRow = $gptByType->get($typeId);
                // Create default section for this type
                $result[] = [
                    'id' => 'new_' . $typeId, // Temporary ID until saved
                    // If there is only one room for this type, do not show "1" in the label.
                    'name' => $type->display_name,
                    'display_label' => $type->display_name,
                    'clone_index' => 0,
                    'accommodation_type_id' => $typeId,
                    'accommodation_type_name' => $type->display_name,
                    'condition_rating' => 'ni',
                    'notes' => '',
                    'location' => '',
                    'photos' => [],
                    'report_content' => '',
                    'has_report' => false,
                    'form_submitted' => false,
                    'completed_components' => 0,
                    'total_components' => $type->components->count(),
                    'gpt_narrative' => $gptRow ? $gptRow->narrative : null,
                    'gpt_observations' => ($gptRow && is_array($gptRow->observations)) ? $gptRow->observations : [],
                    'components' => $type->components->map(function($component) {
                        return [
                            'component_id' => $component->id,
                            'component_key' => $component->key_name,
                            'component_name' => $component->display_name,
                            'material' => '',
                            'defects' => [],
                            'location' => '',
                            'gpt_observations' => [],
                        ];
                    })->toArray(),
                ];
            }
        }
        
        // Add any clones (assessments with clone_index > 0) after their originals
        foreach ($validAssessments as $assessment) {
            if ($assessment->clone_index > 0) {
                $result[] = $this->mapAssessmentToArray($assessment, $gptByType);
            }
        }
        
        Log::info('Real accommodation data', [
            'survey_id' => $survey->id,
            'configured_types' => $configuredTypes->count(),
            'total_assessments' => $accommodationAssessments->count(),
            'valid_assessments' => $validAssessments->count(),
            'result_count' => count($result),
        ]);
        
        return $result;
    }

    /**
     * Header label for an accommodation row (matches survey data UI and Location multi-select options).
     */
    protected function accommodationAssessmentDisplayLabel(SurveyAccommodationAssessment $assessment): string
    {
        $assessment->loadMissing('accommodationType');
        $accommodationTypeName = $assessment->accommodationType->display_name ?? 'Unknown';
        $cloneIndex = (int) ($assessment->clone_index ?? 0);
        $roomCountForType = SurveyAccommodationAssessment::query()
            ->where('survey_id', $assessment->survey_id)
            ->where('accommodation_type_id', $assessment->accommodation_type_id)
            ->count();

        return $roomCountForType <= 1
            ? $accommodationTypeName
            : ($accommodationTypeName . ' ' . ($cloneIndex + 1));
    }
    
    /**
     * Map an assessment to the array format expected by the view.
     *
     * @param SurveyAccommodationAssessment $assessment
     * @param \Illuminate\Support\Collection<int, SurveyAccommodationGptOutput>|null $gptByType keyed by accommodation_type_id
     * @return array
     */
    protected function mapAssessmentToArray($assessment, $gptByType = null): array
    {
        // Map condition rating to string format for display
        $conditionRating = null;
        if ($assessment->condition_rating !== null) {
            $ratingMap = [1 => '1', 2 => '2', 3 => '3'];
            $conditionRating = $ratingMap[$assessment->condition_rating] ?? 'ni';
        } else {
            $conditionRating = 'ni';
        }
        
        $accommodationTypeName = $assessment->accommodationType->display_name ?? 'Unknown';
        $cloneIndex = (int) ($assessment->clone_index ?? 0);
        $displayLabel = $this->accommodationAssessmentDisplayLabel($assessment);
        
        // Build components list in the same order as configured in admin
        $typeComponents = $assessment->accommodationType
            ? $assessment->accommodationType->components
            : collect();

        // Index existing component assessments by component_id for quick lookup
        $componentAssessmentsByComponentId = $assessment->componentAssessments
            ->keyBy('component_id');

        $componentsArray = [];
        $completedComponents = 0;

        foreach ($typeComponents as $component) {
            $componentAssessment = $componentAssessmentsByComponentId->get($component->id);

            $materialValue = '';
            $defectsValues = [];
            $componentLocationValue = '';

            if ($componentAssessment) {
                $hasMaterial = $componentAssessment->material !== null;
                $hasDefectsRelation = $componentAssessment->defects && $componentAssessment->defects->count() > 0;

                if ($hasMaterial) {
                    $materialValue = $componentAssessment->material->value ?? '';
                }

                if ($hasDefectsRelation) {
                    $defectsValues = $componentAssessment->defects->pluck('value')->toArray();
                }

                if ($componentAssessment->relationLoaded('location') && $componentAssessment->location) {
                    $componentLocationValue = (string) ($componentAssessment->location->value ?? '');
                } elseif (!empty($componentAssessment->location_id)) {
                    $componentAssessment->loadMissing('location');
                    $componentLocationValue = (string) ($componentAssessment->location->value ?? '');
                }

                if ($hasMaterial || $hasDefectsRelation) {
                    $completedComponents++;
                }
            }

            $gptObsComp = [];
            if ($componentAssessment && is_array($componentAssessment->gpt_observations ?? null)) {
                $gptObsComp = $componentAssessment->gpt_observations;
            }

            $componentsArray[] = [
                'component_id' => $component->id,
                'component_key' => $component->key_name,
                'component_name' => $component->display_name,
                'material' => $materialValue,
                'defects' => $defectsValues,
                'location' => $componentLocationValue,
                'gpt_observations' => $gptObsComp,
            ];
        }

        $totalComponents = $typeComponents->count();
        
        // Get report content from database (if exists)
        $reportContent = $assessment->report_content ?? '';
        $hasReport = !empty(trim($reportContent));

        $locationValue = '';
        if ($assessment->relationLoaded('location') && $assessment->location) {
            $locationValue = (string) ($assessment->location->value ?? '');
        } elseif ($assessment->location_id) {
            $assessment->loadMissing('location');
            $locationValue = (string) ($assessment->location->value ?? '');
        }

        $gptRow = $gptByType ? $gptByType->get((int) $assessment->accommodation_type_id) : null;
        $gptObservations = ($gptRow && is_array($gptRow->observations)) ? $gptRow->observations : [];

        return [
            'id' => $assessment->id,
            'name' => $displayLabel,
            'display_label' => $displayLabel,
            'clone_index' => $cloneIndex,
            'accommodation_type_id' => $assessment->accommodation_type_id,
            'accommodation_type_name' => $accommodationTypeName,
            'condition_rating' => $conditionRating,
            'notes' => $assessment->notes ?? '',
            'location' => $locationValue,
            'gpt_narrative' => $gptRow ? $gptRow->narrative : null,
            'gpt_observations' => $gptObservations,
            'photos' => $assessment->photos ? $assessment->photos->sortBy('sort_order')->map(function($photo) {
                // Use storage disk URL so production gets absolute URL from APP_URL
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($photo->file_path);
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => $url,
                ];
            })->values()->toArray() : [],
            'report_content' => $reportContent,
            'has_report' => $hasReport,
            'form_submitted' => (bool) $assessment->is_completed,
            'completed_components' => $completedComponents,
            'total_components' => $totalComponents,
            'components' => $componentsArray,
        ];
    }
    

    /**
     * Get available accommodation component types from database.
     * 
     * @return array
     */
    public function getAccommodationComponents(): array
    {
        return SurveyAccommodationComponent::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function($component) {
                return [
                    'key' => $component->key_name,
                    'name' => $component->display_name,
                ];
            })
            ->toArray();
    }

    /**
     * Get accommodation types that have components configured.
     * Only types with at least one component linked will be returned.
     * 
     * @return array Array of accommodation types with their components
     */
    public function getAccommodationTypesWithComponents(): array
    {
        return SurveyAccommodationType::where('is_active', true)
            ->with(['components' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('survey_accommodation_type_components.sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->filter(function($type) {
                return $type->components && $type->components->count() > 0;
            })
            ->map(function($type) {
                return [
                    'id' => $type->id,
                    'key_name' => $type->key_name,
                    'display_name' => $type->display_name,
                    'sort_order' => $type->sort_order,
                    'components' => $type->components->map(function($component) {
                        return [
                            'id' => $component->id,
                            'key_name' => $component->key_name,
                            'display_name' => $component->display_name,
                            'is_required' => $component->pivot->is_required ?? false,
                            'sort_order' => $component->pivot->sort_order ?? 0,
                        ];
                    })->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get material options for a specific component from database.
     * Returns global materials plus any component-scoped materials for the given component key.
     *
     * @param string $componentKey
     * @return array<int, string>
     */
    public function getComponentMaterials(string $componentKey): array
    {
        $materialType = SurveyAccommodationOptionType::where('key_name', 'material')->first();
        if (!$materialType) {
            return [];
        }

        $globalMaterials = SurveyAccommodationOption::where('option_type_id', $materialType->id)
            ->where('scope_type', 'global')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();

        $component = SurveyAccommodationComponent::where('key_name', $componentKey)->first();
        if (!$component) {
            return $globalMaterials;
        }

        $componentMaterials = SurveyAccommodationOption::where('option_type_id', $materialType->id)
            ->where('scope_type', 'component')
            ->where('scope_id', $component->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();

        return array_values(array_unique(array_merge($globalMaterials, $componentMaterials)));
    }

    /**
     * Get defect options for accommodation components from database.
     * Returns global defects plus any component-specific defects when a component key is provided.
     * 
     * @param string|null $componentKey
     * @return array
     */
    public function getComponentDefects(?string $componentKey = null): array
    {
        $defectType = SurveyAccommodationOptionType::where('key_name', 'defects')->first();
        if (!$defectType) {
            return [];
        }

        // Always include global defects
        $globalDefects = SurveyAccommodationOption::where('option_type_id', $defectType->id)
            ->where('scope_type', 'global')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();

        if ($componentKey === null) {
            return $globalDefects;
        }

        // Add component-specific defects for this component (if any)
        $component = SurveyAccommodationComponent::where('key_name', $componentKey)->first();
        if (!$component) {
            return $globalDefects;
        }

        $componentDefects = SurveyAccommodationOption::where('option_type_id', $defectType->id)
            ->where('scope_type', 'component')
            ->where('scope_id', $component->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();

        // Merge, preserving order: globals first, then component-specific, and remove duplicates
        return array_values(array_unique(array_merge($globalDefects, $componentDefects)));
    }

    /**
     * Location options for accommodation rooms/components from database.
     * Returns global locations plus any component-specific locations when a component key is provided.
     *
     * @return array<int, string>
     */
    public function getComponentLocations(?string $componentKey = null): array
    {
        $locationType = SurveyAccommodationOptionType::where('key_name', 'location')->first();
        if (!$locationType) {
            return [];
        }

        $globalLocations = SurveyAccommodationOption::where('option_type_id', $locationType->id)
            ->where('scope_type', 'global')
            ->whereNull('scope_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();

        if ($componentKey === null) {
            return $globalLocations;
        }

        $component = SurveyAccommodationComponent::where('key_name', $componentKey)->first();
        if (!$component) {
            return $globalLocations;
        }

        $componentLocations = SurveyAccommodationOption::where('option_type_id', $locationType->id)
            ->where('scope_type', 'component')
            ->where('scope_id', $component->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();

        return array_values(array_unique(array_merge($globalLocations, $componentLocations)));
    }

    /**
     * Global location options for accommodation rooms (admin-managed).
     *
     * @return array<int, string>
     */
    public function getGlobalLocations(): array
    {
        $locationType = SurveyAccommodationOptionType::where('key_name', 'location')->first();
        if (! $locationType) {
            return [];
        }

        return SurveyAccommodationOption::where('option_type_id', $locationType->id)
            ->where('scope_type', 'global')
            ->whereNull('scope_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Resolve admin option row id for a global location value.
     */
    protected function resolveLocationOptionId(?string $locationValue): ?int
    {
        $trimmed = trim((string) $locationValue);
        if ($trimmed === '') {
            return null;
        }

        $locationType = SurveyAccommodationOptionType::where('key_name', 'location')->first();
        if (! $locationType) {
            return null;
        }

        return $this->findAccommodationOptionId($locationType->id, $trimmed, null);
    }

    protected function resolveComponentLocationOptionId(?string $locationValue, ?int $componentId): ?int
    {
        $trimmed = trim((string) $locationValue);
        if ($trimmed === '') {
            return null;
        }

        $locationType = SurveyAccommodationOptionType::where('key_name', 'location')->first();
        if (! $locationType) {
            return null;
        }

        return $this->findAccommodationOptionId($locationType->id, $trimmed, $componentId);
    }

    /**
     * Save accommodation assessment, persist a plain-text selection summary (no per-room GPT), and regenerate combined component narratives.
     */
    public function saveAccommodationAssessment(Survey $survey, int $accommodationTypeId, array $formData, bool $isClone = false, ?int $assessmentId = null): array
    {
        DB::beginTransaction();
        
        try {
            $accommodationType = SurveyAccommodationType::with('components')->findOrFail($accommodationTypeId);
            
            // Check for clone first, then update
            if ($isClone) {
                // If assessmentId is provided, use it as the source (from clone_ prefix)
                $sourceAssessment = null;
                if ($assessmentId) {
                    $sourceAssessment = SurveyAccommodationAssessment::where('id', $assessmentId)
                        ->where('survey_id', $survey->id)
                        ->where('accommodation_type_id', $accommodationTypeId)
                        ->with(['componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'componentAssessments.location', 'location', 'photos'])
                        ->first();
                }
                
                // If no specific source, find the original (clone_index = 0)
                if (!$sourceAssessment) {
                    $sourceAssessment = SurveyAccommodationAssessment::where('survey_id', $survey->id)
                        ->where('accommodation_type_id', $accommodationTypeId)
                        ->where('clone_index', 0)
                        ->with(['componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'componentAssessments.location', 'location', 'photos'])
                        ->orderBy('id')
                        ->first();
                }
                
                // If still no source, create one
                if (!$sourceAssessment) {
                    $sourceAssessment = new SurveyAccommodationAssessment();
                    $sourceAssessment->survey_id = $survey->id;
                    $sourceAssessment->accommodation_type_id = $accommodationTypeId;
                    $sourceAssessment->clone_index = 0;
                    $sourceAssessment->custom_name = $accommodationType->display_name; // Always use accommodation type name
                    $sourceAssessment->notes = $formData['notes'] ?? null;
                    $sourceAssessment->location_id = $this->resolveLocationOptionId($formData['location'] ?? null);
                    $sourceAssessment->condition_rating = $formData['condition_rating'] ?? null;
                    $sourceAssessment->save();
                    
                    // Save components if provided
                    if (isset($formData['components']) && is_array($formData['components'])) {
                        $this->saveAccommodationComponentAssessments($sourceAssessment, $formData['components']);
                    }
                }
                
                // Calculate next clone index
                $cloneIndex = SurveyAccommodationAssessment::where('survey_id', $survey->id)
                    ->where('accommodation_type_id', $accommodationTypeId)
                    ->where('clone_index', '>', 0)
                    ->max('clone_index') ?? 0;
                $cloneIndex += 1;
                
                // Create the clone
                $assessment = new SurveyAccommodationAssessment();
                $assessment->survey_id = $survey->id;
                $assessment->accommodation_type_id = $accommodationTypeId;
                $assessment->clone_index = $cloneIndex;
                // Always use accommodation type name for clones too - heading should show only the type name
                $assessment->custom_name = $accommodationType->display_name;
                $assessment->notes = $sourceAssessment->notes; // Copy notes from source
                $assessment->location_id = $sourceAssessment->location_id;
                
                // Use condition_rating from formData if provided, otherwise copy from source
                if (isset($formData['condition_rating'])) {
                    $ratingValue = $formData['condition_rating'];
                    if ($ratingValue === 'ni' || $ratingValue === 'NI') {
                        $assessment->condition_rating = null;
                    } else {
                        $assessment->condition_rating = in_array($ratingValue, ['1', '2', '3']) ? (int)$ratingValue : $sourceAssessment->condition_rating;
                    }
                } else {
                    $assessment->condition_rating = $sourceAssessment->condition_rating; // Copy condition rating from source
                }
                $assessment->is_completed = true;
                $assessment->completed_at = now();
                $assessment->save();
                
                Log::info('Accommodation clone created', [
                    'source_id' => $sourceAssessment->id,
                    'clone_id' => $assessment->id,
                    'clone_index' => $cloneIndex,
                ]);
                
                // Copy component assessments from source
                if ($sourceAssessment->componentAssessments->count() > 0) {
                    foreach ($sourceAssessment->componentAssessments as $sourceComponent) {
                        $clonedComponent = $sourceComponent->replicate();
                        $clonedComponent->accommodation_assessment_id = $assessment->id;
                        $clonedComponent->save();
                        
                        // Copy defects relationship
                        if ($sourceComponent->defects->count() > 0) {
                            $clonedComponent->defects()->sync($sourceComponent->defects->pluck('id')->toArray());
                        }
                    }
                }
                
                // Copy photos from source
                if ($sourceAssessment->photos->count() > 0) {
                    $maxSortOrder = \App\Models\SurveyAccommodationPhoto::where('accommodation_assessment_id', $assessment->id)
                        ->max('sort_order') ?? 0;
                    
                    foreach ($sourceAssessment->photos as $sourcePhoto) {
                        $clonedPhoto = $sourcePhoto->replicate();
                        $clonedPhoto->accommodation_assessment_id = $assessment->id;
                        $maxSortOrder += 1;
                        $clonedPhoto->sort_order = $maxSortOrder;
                        $clonedPhoto->save();
                    }
                }

                // User may have edited notes/components on the unsaved clone row — apply form over the copy
                if (array_key_exists('notes', $formData)) {
                    $assessment->notes = $formData['notes'];
                }
                if (array_key_exists('location', $formData)) {
                    $assessment->location_id = $this->resolveLocationOptionId($formData['location'] ?? null);
                }
                $assessment->save();
                if (isset($formData['components']) && is_array($formData['components']) && ! empty($formData['components'])) {
                    $this->saveAccommodationComponentAssessments($assessment, $formData['components']);
                }
                $assessment->load(['componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'location', 'photos']);
            } elseif ($assessmentId) {
                // Update existing assessment (not a clone)
                $assessment = SurveyAccommodationAssessment::where('id', $assessmentId)
                    ->where('survey_id', $survey->id)
                    ->where('accommodation_type_id', $accommodationTypeId)
                    ->firstOrFail();
            } else {
                // New assessment or find existing original
                $assessment = SurveyAccommodationAssessment::where('survey_id', $survey->id)
                    ->where('accommodation_type_id', $accommodationTypeId)
                    ->where('clone_index', 0)
                    ->first();
                
                if (!$assessment) {
                    $assessment = new SurveyAccommodationAssessment();
                    $assessment->survey_id = $survey->id;
                    $assessment->accommodation_type_id = $accommodationTypeId;
                    $assessment->clone_index = 0;
                }
            }

            // Only update these fields if not cloning (clones already have values set above)
            if (!$isClone) {
                // Map condition_rating from string to integer if provided
                $conditionRating = null;
                if (isset($formData['condition_rating'])) {
                    $ratingValue = $formData['condition_rating'];
                    if ($ratingValue === 'ni' || $ratingValue === 'NI') {
                        $conditionRating = null;
                    } else {
                        $conditionRating = in_array($ratingValue, ['1', '2', '3']) ? (int)$ratingValue : null;
                    }
                }
                
                // Always use accommodation type name - never allow custom names or "Select Section"
                // The heading should always show only the accommodation type name
                $assessment->custom_name = $accommodationType->display_name;
                $assessment->notes = $formData['notes'] ?? null;
                if (array_key_exists('location', $formData)) {
                    $assessment->location_id = $this->resolveLocationOptionId($formData['location'] ?? null);
                }
                $assessment->condition_rating = $conditionRating ?? $assessment->condition_rating;
                $assessment->is_completed = true;
                $assessment->completed_at = now();
                $assessment->save();

                // Save components for new/updated assessment
                if (isset($formData['components']) && is_array($formData['components']) && !empty($formData['components'])) {
                    // Use components from form data
                    $this->saveAccommodationComponentAssessments($assessment, $formData['components']);
                } else {
                    // If no components provided, initialize from accommodation type configuration
                    $this->initializeComponentsFromType($assessment, $accommodationType);
                }
            } else {
                // For clones, data is already set above, just ensure completed status
                if (!$assessment->is_completed) {
                    $assessment->is_completed = true;
                    $assessment->completed_at = now();
                    $assessment->save();
                }
                // Components are already copied above, no need to save again unless updating
            }

            $assessment->load(['accommodationType', 'componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'location']);

            $reportContent = $this->buildAccommodationSelectionsSummary($assessment);
            $assessment->report_content = $reportContent;
            $assessment->save();

            DB::commit();

            $gptResult = $this->regenerateAccommodationTypeCombinedGpt($survey, $accommodationTypeId);

            // Regeneration writes type-wide GPT bullets onto every component row; re-apply this room's
            // form payload so surveyor-edited per-component fields (including merged GPT observations) persist.
            if (isset($formData['components']) && is_array($formData['components']) && $formData['components'] !== []) {
                $assessment->refresh();
                $this->saveAccommodationComponentAssessments($assessment, $formData['components']);
            }

            return [
                'success' => true,
                'assessment' => $assessment,
                'report_content' => $reportContent,
                'report_generation_error' => null,
                'gpt_narrative' => $gptResult['gpt_narrative'],
                'gpt_observations' => $gptResult['gpt_observations'],
                'gpt_component_observations' => $gptResult['gpt_component_observations'] ?? [],
                'gpt_room_component_observations' => $gptResult['gpt_room_component_observations'] ?? [],
                'gpt_generation_error' => $gptResult['gpt_generation_error'],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save accommodation assessment', [
                'survey_id' => $survey->id,
                'accommodation_type_id' => $accommodationTypeId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Initialize component assessments from accommodation type configuration.
     */
    protected function initializeComponentsFromType(SurveyAccommodationAssessment $assessment, SurveyAccommodationType $accommodationType): void
    {
        // Get components configured for this accommodation type
        $typeComponents = $accommodationType->components;
        
        if ($typeComponents->isEmpty()) {
            Log::info('No components configured for accommodation type', [
                'accommodation_type_id' => $accommodationType->id,
                'assessment_id' => $assessment->id,
            ]);
            return;
        }
        
        // Create empty component assessments for each configured component
        foreach ($typeComponents as $component) {
            $existingAssessment = SurveyAccommodationComponentAssessment::where('accommodation_assessment_id', $assessment->id)
                ->where('component_id', $component->id)
                ->first();
            
            if (!$existingAssessment) {
                $componentAssessment = new SurveyAccommodationComponentAssessment();
                $componentAssessment->accommodation_assessment_id = $assessment->id;
                $componentAssessment->component_id = $component->id;
                $componentAssessment->save();
            }
        }
        
        Log::info('Initialized components from accommodation type', [
            'accommodation_type_id' => $accommodationType->id,
            'assessment_id' => $assessment->id,
            'components_count' => $typeComponents->count(),
        ]);
    }

    /**
     * Save component assessments for an accommodation assessment.
     */
    protected function saveAccommodationComponentAssessments(SurveyAccommodationAssessment $assessment, array $components): void
    {
        $materialType = SurveyAccommodationOptionType::where('key_name', 'material')->first();
        $defectType = SurveyAccommodationOptionType::where('key_name', 'defects')->first();
        
        if (!$materialType || !$defectType) {
            Log::warning('Accommodation option types not found');
            return;
        }

        foreach ($components as $componentData) {
            $componentKey = $componentData['component_key'] ?? null;
            // Skip if component_key is missing or empty - allows incomplete component data
            if (empty($componentKey)) {
                continue;
            }

            $component = SurveyAccommodationComponent::where('key_name', $componentKey)
                ->where('is_active', true)
                ->first();

            if (!$component) {
                Log::warning('Accommodation component not found', ['component_key' => $componentKey]);
                continue;
            }

            $componentAssessment = SurveyAccommodationComponentAssessment::where('accommodation_assessment_id', $assessment->id)
                ->where('component_id', $component->id)
                ->first();

            if (!$componentAssessment) {
                $componentAssessment = new SurveyAccommodationComponentAssessment();
                $componentAssessment->accommodation_assessment_id = $assessment->id;
                $componentAssessment->component_id = $component->id;
            }

            $materialValue = $componentData['material'] ?? null;
            if ($materialValue) {
                $materialOptionId = $this->findAccommodationOptionId($materialType->id, $materialValue, $component->id);
                $componentAssessment->material_id = $materialOptionId;
            }

            if (array_key_exists('location', $componentData)) {
                $componentAssessment->location_id = $this->resolveComponentLocationOptionId($componentData['location'] ?? null, $component->id);
            }

            if (array_key_exists('gpt_observations', $componentData)) {
                $raw = $componentData['gpt_observations'];
                $bullets = [];
                if (is_array($raw)) {
                    foreach ($raw as $line) {
                        $t = is_string($line) ? trim($line) : '';
                        if ($t !== '') {
                            $bullets[] = $t;
                        }
                    }
                }
                $componentAssessment->gpt_observations = $bullets === [] ? null : $bullets;
            }

            $componentAssessment->save();

            if (isset($componentData['defects']) && is_array($componentData['defects'])) {
                $defectOptionIds = $this->findAccommodationDefectOptionIds(
                    $defectType->id,
                    $componentData['defects'],
                    $component->id
                );
                $componentAssessment->defects()->sync($defectOptionIds);
            }
        }
    }

    /**
     * Find accommodation option ID by value, option type, and component.
     */
    protected function findAccommodationOptionId(int $optionTypeId, string $value, ?int $componentId = null): ?int
    {
        if (!$value) {
            return null;
        }

        $query = SurveyAccommodationOption::where('option_type_id', $optionTypeId)
            ->where('value', $value)
            ->where('is_active', true);

        if ($componentId) {
            $componentOption = (clone $query)
                ->where('scope_type', 'component')
                ->where('scope_id', $componentId)
                ->first();
            
            if ($componentOption) {
                return $componentOption->id;
            }
        }

        $globalOption = (clone $query)
            ->where('scope_type', 'global')
            ->whereNull('scope_id')
            ->first();

        return $globalOption->id ?? null;
    }

    /**
     * Find accommodation defect option IDs by values.
     * Looks for component-specific defects first (when component ID provided), then global ones.
     */
    protected function findAccommodationDefectOptionIds(int $defectOptionTypeId, array $defectValues, ?int $componentId = null): array
    {
        if (empty($defectValues)) {
            return [];
        }

        $query = SurveyAccommodationOption::where('option_type_id', $defectOptionTypeId)
            ->whereIn('value', $defectValues)
            ->where('is_active', true);

        // Match both global and component-specific defects (when component is known)
        $query->where(function ($q) use ($componentId) {
            $q->where(function ($q2) {
                $q2->where('scope_type', 'global')
                   ->whereNull('scope_id');
            });

            if ($componentId) {
                $q->orWhere(function ($q3) use ($componentId) {
                    $q3->where('scope_type', 'component')
                       ->where('scope_id', $componentId);
                });
            }
        });

        return $query->pluck('id')->toArray();
    }

    /**
     * Apply selections from a regular "Accommodation Components" section (acc_component__{key})
     * onto the per-room accommodation component assessments so values persist across refresh.
     *
     * By default, this only fills blanks (does not overwrite room-specific selections).
     *
     * The section "Location" control lists accommodation rooms (e.g. Bedroom 1); only rows whose
     * display label is in {@see $roomDisplayLabels} are updated. It is not written to component location_id.
     *
     * @param array{material?: string, defects?: array<int,string>} $selections
     * @param list<string> $roomDisplayLabels
     */
    public function applyComponentSectionSelectionsToSurveyAccommodations(
        Survey $survey,
        string $componentKey,
        array $selections,
        bool $onlyWhenEmpty = true,
        array $roomDisplayLabels = []
    ): void {
        $componentKey = trim($componentKey);
        if ($componentKey === '') {
            return;
        }

        $component = SurveyAccommodationComponent::where('key_name', $componentKey)
            ->where('is_active', true)
            ->first();

        if (! $component) {
            return;
        }

        $materialType = SurveyAccommodationOptionType::where('key_name', 'material')->first();
        $defectType = SurveyAccommodationOptionType::where('key_name', 'defects')->first();

        if (! $materialType || ! $defectType) {
            return;
        }

        if ($roomDisplayLabels === []) {
            return;
        }

        $materialVal = isset($selections['material']) ? trim((string) $selections['material']) : '';
        $defectVals = isset($selections['defects']) && is_array($selections['defects'])
            ? array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $selections['defects']), static fn ($v) => $v !== ''))
            : [];

        $allowedRooms = array_flip(array_map(static fn ($s) => trim((string) $s), $roomDisplayLabels));

        // Only target accommodation assessments whose type includes this component.
        $assessments = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->whereHas('accommodationType.components', function ($q) use ($component) {
                $q->where('survey_accommodation_components.id', $component->id);
            })
            ->with([
                'accommodationType',
                'componentAssessments' => function ($q) use ($component) {
                    $q->where('component_id', $component->id)->with(['material', 'defects', 'location']);
                },
            ])
            ->get();

        foreach ($assessments as $assessment) {
            $rowLabel = $this->accommodationAssessmentDisplayLabel($assessment);
            if (! isset($allowedRooms[$rowLabel])) {
                continue;
            }

            $componentAssessment = $assessment->componentAssessments->first();
            if (! $componentAssessment) {
                $componentAssessment = new SurveyAccommodationComponentAssessment();
                $componentAssessment->accommodation_assessment_id = $assessment->id;
                $componentAssessment->component_id = $component->id;
            }

            $hasMaterial = ! empty($componentAssessment->material_id);
            $hasDefects = $componentAssessment->exists
                ? ($componentAssessment->defects && $componentAssessment->defects->count() > 0)
                : false;

            if ($materialVal !== '' && (! $onlyWhenEmpty || ! $hasMaterial)) {
                $componentAssessment->material_id = $this->findAccommodationOptionId($materialType->id, $materialVal, $component->id);
            }

            // Save before syncing defects if it's a new row.
            if (! $componentAssessment->exists) {
                $componentAssessment->save();
                $componentAssessment->refresh();
            } else {
                $componentAssessment->save();
            }

            if (! empty($defectVals) && (! $onlyWhenEmpty || ! $hasDefects)) {
                $defectOptionIds = $this->findAccommodationDefectOptionIds($defectType->id, $defectVals, $component->id);
                $componentAssessment->defects()->sync($defectOptionIds);
            }
        }
    }

    /**
     * Copy photos uploaded on an "Accommodation Components" section assessment onto
     * the matching per-room Accommodation assessments (rooms in $roomDisplayLabels).
     *
     * This allows component form photos to immediately appear in the Accommodation form UI
     * for the selected rooms.
     *
     * @return array<int, array<int, array{ id:int, file_path:string, file_name:string, url:string }>> keyed by accommodation_assessment_id
     */
    public function syncComponentSectionPhotosToSurveyAccommodationsPhotos(
        Survey $survey,
        string $componentKey,
        array $roomDisplayLabels,
        \App\Models\SurveySectionAssessment $sourceSectionAssessment
    ): array {
        $componentKey = trim($componentKey);
        if ($componentKey === '') {
            return [];
        }

        $roomDisplayLabels = array_values(array_unique(array_filter(array_map(static function ($s) {
            return trim((string) $s);
        }, $roomDisplayLabels), static function ($s) {
            return $s !== '';
        })));

        if ($roomDisplayLabels === []) {
            return [];
        }

        // Source photos (uploaded via component section form)
        $sourcePhotos = \App\Models\SurveySectionPhoto::query()
            ->where('section_assessment_id', $sourceSectionAssessment->id)
            ->orderBy('sort_order')
            ->get();

        if ($sourcePhotos->isEmpty()) {
            return [];
        }

        $component = SurveyAccommodationComponent::where('key_name', $componentKey)
            ->where('is_active', true)
            ->first();

        if (! $component) {
            return [];
        }

        $allowedByNormalized = [];
        foreach ($roomDisplayLabels as $lbl) {
            $norm = strtolower(trim((string) $lbl));
            if ($norm !== '') {
                $allowedByNormalized[$norm] = true;
            }
        }

        $disk = Storage::disk('public');
        $out = [];

        // Only rooms (accommodation assessments) whose accommodation type includes this component.
        $assessments = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->whereHas('accommodationType.components', function ($q) use ($component) {
                $q->where('survey_accommodation_components.id', $component->id);
            })
            ->get();

        foreach ($assessments as $assessment) {
            $rowLabel = $this->accommodationAssessmentDisplayLabel($assessment);
            $rowNorm = strtolower(trim($rowLabel));
            if ($rowNorm === '' || ! isset($allowedByNormalized[$rowNorm])) {
                continue;
            }

            $destDir = "accommodation-photos/{$survey->id}/{$assessment->id}";
            try {
                $disk->makeDirectory($destDir);
            } catch (\Throwable $e) {
                Log::warning('Failed to create accommodation photo dir', [
                    'assessment_id' => $assessment->id,
                    'destDir' => $destDir,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            $nextSortOrder = (\App\Models\SurveyAccommodationPhoto::where('accommodation_assessment_id', $assessment->id)->max('sort_order') ?? -1);
            $addedForThisAssessment = [];

            foreach ($sourcePhotos as $photo) {
                $sourceRelPath = (string) ($photo->file_path ?? '');
                if ($sourceRelPath === '') {
                    continue;
                }
                if (! $disk->exists($sourceRelPath)) {
                    continue;
                }

                $destRelPath = $destDir . '/' . basename($sourceRelPath);

                // Avoid duplicating the same copied photo on repeated saves.
                $exists = \App\Models\SurveyAccommodationPhoto::query()
                    ->where('accommodation_assessment_id', $assessment->id)
                    ->where('file_path', $destRelPath)
                    ->exists();
                if ($exists) {
                    continue;
                }

                try {
                    $disk->copy($sourceRelPath, $destRelPath);
                } catch (\Throwable $e) {
                    Log::warning('Failed to copy accommodation photo', [
                        'source' => $sourceRelPath,
                        'dest' => $destRelPath,
                        'assessment_id' => $assessment->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }

                $nextSortOrder += 1;
                $created = \App\Models\SurveyAccommodationPhoto::create([
                    'accommodation_assessment_id' => $assessment->id,
                    'file_path' => $destRelPath,
                    'file_name' => (string) ($photo->file_name ?? ''),
                    'file_size' => (int) ($photo->file_size ?? 0),
                    'mime_type' => (string) ($photo->mime_type ?? ''),
                    'sort_order' => $nextSortOrder,
                ]);

                $out[$assessment->id][] = [
                    'id' => $created->id,
                    'file_path' => $created->file_path,
                    'file_name' => $created->file_name,
                    'url' => $disk->url($created->file_path),
                ];

                $addedForThisAssessment[] = $created->id;
            }
        }

        return $out;
    }

    /**
     * Save photos for an accommodation assessment.
     */
    public function saveAccommodationPhotos(SurveyAccommodationAssessment $assessment, array $photos): void
    {
        if (empty($photos)) {
            return;
        }

        $surveyId = $assessment->survey_id;
        $assessmentId = $assessment->id;
        $storagePath = "accommodation-photos/{$surveyId}/{$assessmentId}";
        
        // Ensure storage directory exists
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($storagePath);
        
        $maxSortOrder = \App\Models\SurveyAccommodationPhoto::where('accommodation_assessment_id', $assessmentId)
            ->max('sort_order') ?? -1;
        
        foreach ($photos as $index => $photo) {
            try {
                if (!$photo || !$photo->isValid()) {
                    \Illuminate\Support\Facades\Log::warning('Invalid photo skipped', [
                        'index' => $index,
                        'assessment_id' => $assessmentId,
                    ]);
                    continue;
                }
                
                $extension = $photo->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '_' . $index . '.' . $extension;
                $filePath = $photo->storeAs($storagePath, $filename, 'public');
                
                if (!$filePath) {
                    \Illuminate\Support\Facades\Log::error('Failed to store accommodation photo', [
                        'index' => $index,
                        'assessment_id' => $assessmentId,
                        'filename' => $filename,
                    ]);
                    continue;
                }
                
                \App\Models\SurveyAccommodationPhoto::create([
                    'accommodation_assessment_id' => $assessmentId,
                    'file_path' => $filePath,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error saving accommodation photo', [
                    'index' => $index,
                    'assessment_id' => $assessmentId,
                    'error' => $e->getMessage(),
                ]);
                // Continue with next photo instead of failing completely
                continue;
            }
        }
    }

    /**
     * Regenerate combined GPT for every accommodation type on this survey that includes the given component.
     * Used when a surveyor saves an "Accommodation Components" section (acc_component__{key}) so room rows
     * and persisted component observations stay aligned without opening the Accommodation form.
     *
     * @return list<array{accommodation_type_id: int, gpt_narrative: ?string, gpt_observations: array<int, string>, gpt_component_observations: array<string, array<int, string>>, gpt_generation_error: ?string}>
     */
    public function regenerateAccommodationGptForTypesUsingComponent(Survey $survey, string $componentKey): array
    {
        $componentKey = trim($componentKey);
        if ($componentKey === '') {
            return [];
        }

        $component = SurveyAccommodationComponent::where('key_name', $componentKey)
            ->where('is_active', true)
            ->first();

        if (! $component) {
            return [];
        }

        $typeIds = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->whereHas('accommodationType.components', function ($q) use ($component) {
                $q->where('survey_accommodation_components.id', $component->id);
            })
            ->distinct()
            ->pluck('accommodation_type_id')
            ->filter()
            ->values();

        $out = [];
        foreach ($typeIds as $typeId) {
            $tid = (int) $typeId;
            if ($tid <= 0) {
                continue;
            }
            $gpt = $this->regenerateAccommodationTypeCombinedGpt($survey, $tid);
            $out[] = [
                'accommodation_type_id' => $tid,
                'gpt_narrative' => $gpt['gpt_narrative'],
                'gpt_observations' => $gpt['gpt_observations'],
                'gpt_component_observations' => $gpt['gpt_component_observations'],
                'gpt_generation_error' => $gpt['gpt_generation_error'],
            ];
        }

        return $out;
    }

    /**
     * Regenerate combined GPT narrative + observations for an accommodation type (all non-empty rooms).
     *
     * @return array{gpt_narrative: ?string, gpt_observations: array<int, string>, gpt_component_observations: array<string, array<int, string>>, gpt_generation_error: ?string}
     */
    public function regenerateAccommodationTypeCombinedGpt(Survey $survey, int $accommodationTypeId): array
    {
        $assessments = $this->loadAssessmentsForAccommodationType($survey, $accommodationTypeId);
        $rooms = [];
        foreach ($assessments as $a) {
            if (!$this->assessmentHasReportableData($a)) {
                continue;
            }
            $rooms[] = $this->buildRoomPayloadForAssessment($a);
        }

        if ($rooms === []) {
            SurveyAccommodationGptOutput::query()
                ->where('survey_id', $survey->id)
                ->where('accommodation_type_id', $accommodationTypeId)
                ->delete();
            $this->clearComponentAssessmentGptObservationsForType($survey, $accommodationTypeId);

            return [
                'gpt_narrative' => null,
                'gpt_observations' => [],
                'gpt_component_observations' => [],
                'gpt_room_component_observations' => [],
                'gpt_generation_error' => null,
            ];
        }

        $type = SurveyAccommodationType::with(['components' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->findOrFail($accommodationTypeId);

        $payload = [
            'accommodation_type' => $type->display_name,
            'component_keys' => $type->components->pluck('key_name')->values()->all(),
            'rooms' => $rooms,
        ];

        try {
            $out = $this->chatGPTService->generateAccommodationCombinedReport($payload);

            $normalizedByKey = [];
            foreach ($type->components as $component) {
                $key = $component->key_name;
                $normalizedByKey[$key] = $out['component_observations'][$key] ?? [];
            }

            SurveyAccommodationGptOutput::query()->updateOrCreate(
                [
                    'survey_id' => $survey->id,
                    'accommodation_type_id' => $accommodationTypeId,
                ],
                [
                    'narrative' => $out['narrative'],
                    'observations' => $out['observations'],
                ]
            );

            // IMPORTANT: combined component bullets are cross-room by definition. Do not copy the same bullets
            // onto every room row; generate room-specific bullets instead so clones differ.
            $this->syncRoomSpecificComponentAssessmentGptObservations($survey, $accommodationTypeId, $type);

            return [
                'gpt_narrative' => $out['narrative'],
                'gpt_observations' => $out['observations'],
                'gpt_component_observations' => $normalizedByKey,
                'gpt_room_component_observations' => $this->buildRoomComponentGptObservationsPayloadForType($survey, $accommodationTypeId, $type),
                'gpt_generation_error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to regenerate accommodation combined GPT output', [
                'survey_id' => $survey->id,
                'accommodation_type_id' => $accommodationTypeId,
                'error' => $e->getMessage(),
            ]);

            return [
                'gpt_narrative' => null,
                'gpt_observations' => [],
                'gpt_component_observations' => [],
                'gpt_room_component_observations' => [],
                'gpt_generation_error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Per-room GPT component bullets as persisted on component assessments (for immediate UI refresh).
     *
     * @return list<array{accommodation_assessment_id: int, component_observations: array<string, list<string>>}>
     */
    public function buildRoomComponentGptObservationsPayloadForType(Survey $survey, int $accommodationTypeId, ?SurveyAccommodationType $type = null): array
    {
        $type = $type ?? SurveyAccommodationType::with(['components' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->find($accommodationTypeId);

        if (! $type) {
            return [];
        }

        $assessments = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->with(['componentAssessments' => function ($q) {
                $q->with('component');
            }])
            ->orderBy('clone_index')
            ->orderBy('id')
            ->get();

        $out = [];
        foreach ($assessments as $assessment) {
            $byKey = [];
            foreach ($type->components as $component) {
                $ca = $assessment->componentAssessments->firstWhere('component_id', $component->id);
                $raw = $ca ? ($ca->gpt_observations ?? null) : null;
                $bullets = is_array($raw)
                    ? array_values(array_filter(array_map(static fn ($s) => trim((string) $s), $raw), static fn ($s) => $s !== ''))
                    : [];
                $byKey[$component->key_name] = $bullets;
            }
            $out[] = [
                'accommodation_assessment_id' => (int) $assessment->id,
                'component_observations' => $byKey,
            ];
        }

        return $out;
    }

    /**
     * Generate per-room (per accommodation assessment) component bullets and persist them onto
     * SurveyAccommodationComponentAssessment.gpt_observations, so clones can differ.
     */
    protected function syncRoomSpecificComponentAssessmentGptObservations(
        Survey $survey,
        int $accommodationTypeId,
        SurveyAccommodationType $type
    ): void {
        $assessments = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->get();

        $componentKeys = $type->components->pluck('key_name')->values()->all();

        foreach ($assessments as $assessment) {
            if (!$this->assessmentHasReportableData($assessment)) {
                continue;
            }

            try {
                $roomPayload = $this->buildRoomPayloadForAssessment($assessment);
                $resp = $this->chatGPTService->generateAccommodationRoomComponentObservations([
                    'accommodation_type' => $type->display_name,
                    'component_keys' => $componentKeys,
                    'room' => $roomPayload,
                ]);

                $byKey = is_array($resp['component_observations'] ?? null) ? $resp['component_observations'] : [];

                foreach ($type->components as $component) {
                    $key = $component->key_name;
                    $bullets = $byKey[$key] ?? [];
                    if (!is_array($bullets)) {
                        $bullets = [];
                    }
                    $bullets = array_values(array_filter(array_map('strval', $bullets), static fn ($s) => trim($s) !== ''));

                    $componentAssessment = SurveyAccommodationComponentAssessment::query()
                        ->where('accommodation_assessment_id', $assessment->id)
                        ->where('component_id', $component->id)
                        ->first();

                    if (!$componentAssessment) {
                        continue;
                    }

                    $componentAssessment->gpt_observations = $bullets === [] ? null : $bullets;
                    $componentAssessment->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to generate room-specific component GPT observations', [
                    'survey_id' => $survey->id,
                    'accommodation_type_id' => $accommodationTypeId,
                    'accommodation_assessment_id' => $assessment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Clear GPT observation bullets persisted on each component assessment row for this accommodation type.
     */
    protected function clearComponentAssessmentGptObservationsForType(Survey $survey, int $accommodationTypeId): void
    {
        $assessmentIds = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->pluck('id');

        if ($assessmentIds->isEmpty()) {
            return;
        }

        SurveyAccommodationComponentAssessment::query()
            ->whereIn('accommodation_assessment_id', $assessmentIds)
            ->update(['gpt_observations' => null]);
    }

    /**
     * @param array<string, array<int, string>> $observationsByComponentKey
     */
    protected function syncComponentAssessmentGptObservationsFromKeys(Survey $survey, int $accommodationTypeId, array $observationsByComponentKey): void
    {
        $type = SurveyAccommodationType::with(['components' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->find($accommodationTypeId);

        if (! $type) {
            return;
        }

        $assessments = SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->get();

        foreach ($assessments as $assessment) {
            foreach ($type->components as $component) {
                $key = $component->key_name;
                $bullets = $observationsByComponentKey[$key] ?? [];
                if (! is_array($bullets)) {
                    $bullets = [];
                }

                $componentAssessment = SurveyAccommodationComponentAssessment::query()
                    ->where('accommodation_assessment_id', $assessment->id)
                    ->where('component_id', $component->id)
                    ->first();

                if (! $componentAssessment) {
                    continue;
                }

                $componentAssessment->gpt_observations = $bullets;
                $componentAssessment->save();
            }
        }
    }

    /**
     * Whether this room row has any data worth sending to GPT (otherwise omitted from combined payload).
     */
    protected function assessmentHasReportableData(SurveyAccommodationAssessment $assessment): bool
    {
        $assessment->loadMissing([
            'componentAssessments.component',
            'componentAssessments.material',
            'componentAssessments.defects',
            'componentAssessments.location',
            'location',
            'accommodationType.components',
        ]);

        if (trim((string) ($assessment->notes ?? '')) !== '') {
            return true;
        }

        if ($assessment->condition_rating !== null) {
            return true;
        }

        $roomLoc = '';
        if ($assessment->relationLoaded('location') && $assessment->location) {
            $roomLoc = trim((string) ($assessment->location->value ?? ''));
        } elseif ($assessment->location_id) {
            $assessment->loadMissing('location');
            $roomLoc = trim((string) ($assessment->location->value ?? ''));
        }
        if ($roomLoc !== '') {
            return true;
        }

        foreach ($assessment->componentAssessments as $ca) {
            $material = $ca->material ? trim((string) ($ca->material->value ?? '')) : '';
            $defects = $ca->defects ? $ca->defects->pluck('value')->values()->all() : [];
            $meaningfulDefects = array_filter($defects, function ($d) {
                return $d !== null && $d !== '' && ! in_array($d, ['None', 'No Defects'], true);
            });

            $compLoc = '';
            if ($ca->relationLoaded('location') && $ca->location) {
                $compLoc = trim((string) ($ca->location->value ?? ''));
            } elseif (! empty($ca->location_id)) {
                $ca->loadMissing('location');
                $compLoc = trim((string) ($ca->location->value ?? ''));
            }

            if ($material !== '' || ! empty($meaningfulDefects) || $compLoc !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * One room entry for the combined ChatGPT payload (all components in admin order).
     *
     * @return array<string, mixed>
     */
    protected function buildRoomPayloadForAssessment(SurveyAccommodationAssessment $assessment): array
    {
        $assessment->loadMissing([
            'accommodationType.components' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            },
            'componentAssessments.component',
            'componentAssessments.material',
            'componentAssessments.defects',
            'componentAssessments.location',
            'location',
        ]);

        $typeComponents = $assessment->accommodationType
            ? $assessment->accommodationType->components
            : collect();

        $byComponentId = $assessment->componentAssessments->keyBy('component_id');

        $components = [];
        foreach ($typeComponents as $component) {
            $ca = $byComponentId->get($component->id);
            $material = '';
            $defects = [];
            $location = '';
            if ($ca) {
                if ($ca->material) {
                    $material = (string) ($ca->material->value ?? '');
                }
                if ($ca->defects && $ca->defects->count() > 0) {
                    $defects = $ca->defects->pluck('value')->values()->all();
                }
                if ($ca->relationLoaded('location') && $ca->location) {
                    $location = (string) ($ca->location->value ?? '');
                } elseif (! empty($ca->location_id)) {
                    $ca->loadMissing('location');
                    $location = (string) ($ca->location->value ?? '');
                }
            }
            $components[] = [
                'component_key' => $component->key_name,
                'component_name' => $component->display_name,
                'material' => $material,
                'defects' => $defects,
                'location' => $location,
            ];
        }

        $roomLocation = '';
        if ($assessment->relationLoaded('location') && $assessment->location) {
            $roomLocation = (string) ($assessment->location->value ?? '');
        } elseif ($assessment->location_id) {
            $assessment->loadMissing('location');
            $roomLocation = (string) ($assessment->location->value ?? '');
        }

        return [
            'room_label' => $this->accommodationNumberedDisplayName($assessment),
            'notes' => (string) ($assessment->notes ?? ''),
            'location' => $roomLocation,
            'condition_rating' => $assessment->condition_rating !== null ? (string) $assessment->condition_rating : null,
            'components' => $components,
        ];
    }

    /**
     * Saved combined narratives per accommodation type + component (for survey data page).
     *
     * @return array<int, array<string, array{content: string, component_id: int, input_hash: string|null}>>
     */
    public function getComponentSummariesForSurvey(Survey $survey): array
    {
        $rows = SurveyAccommodationComponentSummary::query()
            ->where('survey_id', $survey->id)
            ->with('component:id,key_name')
            ->get();

        $out = [];
        foreach ($rows as $row) {
            $keyName = $row->component->key_name ?? null;
            if ($keyName === null) {
                continue;
            }
            $out[$row->accommodation_type_id][$keyName] = [
                'content' => (string) ($row->content ?? ''),
                'component_id' => (int) $row->component_id,
                'input_hash' => $row->input_hash,
            ];
        }

        return $out;
    }

    /**
     * Persist manually edited combined narrative (or after client-side generate).
     */
    public function saveComponentGroupSummaryContent(
        Survey $survey,
        int $accommodationTypeId,
        int $componentId,
        string $content
    ): SurveyAccommodationComponentSummary {
        $summary = SurveyAccommodationComponentSummary::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'accommodation_type_id' => $accommodationTypeId,
                'component_id' => $componentId,
            ],
            [
                'content' => $content,
                'input_hash' => null,
            ]
        );

        return $summary;
    }

    /**
     * Regenerate GPT combined narratives for each component of an accommodation type (all rooms).
     *
     * @return array<string, array{content: string, input_hash: string}>
     */
    public function regenerateComponentGroupSummariesForType(Survey $survey, int $accommodationTypeId): array
    {
        $type = SurveyAccommodationType::with(['components' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->findOrFail($accommodationTypeId);

        $assessments = $this->loadAssessmentsForAccommodationType($survey, $accommodationTypeId);

        $byKey = [];

        foreach ($type->components as $component) {
            $one = $this->upsertGroupSummaryForComponent($survey, $type, $component, $assessments);
            if ($one !== null) {
                $byKey[$component->key_name] = $one;
            }
        }

        return $byKey;
    }

    /**
     * Regenerate a single component's combined narrative (all rooms of that type).
     *
     * @return array{content: string, input_hash: string, component_key: string}
     */
    public function regenerateSingleComponentGroupSummary(Survey $survey, int $accommodationTypeId, int $componentId): array
    {
        $type = SurveyAccommodationType::findOrFail($accommodationTypeId);
        $component = SurveyAccommodationComponent::where('is_active', true)->findOrFail($componentId);

        $attached = $type->components()->where('survey_accommodation_components.id', $component->id)->exists();
        if (!$attached) {
            throw new \InvalidArgumentException('This component is not configured for this accommodation type.');
        }

        $assessments = $this->loadAssessmentsForAccommodationType($survey, $accommodationTypeId);
        $one = $this->upsertGroupSummaryForComponent($survey, $type, $component, $assessments);
        if ($one === null) {
            throw new \RuntimeException('No saved room data for this component yet. Save at least one accommodation section first.');
        }

        return array_merge($one, ['component_key' => $component->key_name]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, SurveyAccommodationAssessment>
     */
    protected function loadAssessmentsForAccommodationType(Survey $survey, int $accommodationTypeId)
    {
        return SurveyAccommodationAssessment::query()
            ->where('survey_id', $survey->id)
            ->where('accommodation_type_id', $accommodationTypeId)
            ->with([
                'componentAssessments.component',
                'componentAssessments.material',
                'componentAssessments.defects',
                'componentAssessments.location',
                'location',
                'accommodationType.components' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->orderBy('clone_index')
            ->get();
    }

    /**
     * @param \Illuminate\Support\Collection<int, SurveyAccommodationAssessment> $assessments
     * @return array{content: string, input_hash: string}|null
     */
    protected function upsertGroupSummaryForComponent(
        Survey $survey,
        SurveyAccommodationType $type,
        SurveyAccommodationComponent $component,
        $assessments
    ): ?array {
        $rooms = [];
        foreach ($assessments as $assessment) {
            $ca = $assessment->componentAssessments->firstWhere('component_id', $component->id);
            if (!$ca) {
                continue;
            }
            $assessment->loadMissing('location');
            $material = $ca->material !== null ? ($ca->material->value ?? '') : '';
            $defects = $ca->defects->pluck('value')->toArray();
            $rating = $assessment->condition_rating;
            $locationStr = '';
            // Prefer component-specific location override; fall back to room global location
            if ($ca->relationLoaded('location') && $ca->location) {
                $locationStr = (string) ($ca->location->value ?? '');
            } elseif (!empty($ca->location_id)) {
                $ca->loadMissing('location');
                $locationStr = (string) ($ca->location->value ?? '');
            } elseif ($assessment->relationLoaded('location') && $assessment->location) {
                $locationStr = (string) ($assessment->location->value ?? '');
            }
            $rooms[] = [
                'room_label' => $this->accommodationNumberedDisplayName($assessment),
                'material' => $material,
                'defects' => $defects,
                'notes' => $assessment->notes ?? '',
                'location' => $locationStr,
                'condition_rating' => $rating !== null ? (string) $rating : null,
            ];
        }

        if ($rooms === []) {
            return null;
        }

        $payload = [
            'accommodation_type' => $type->display_name,
            'component_name' => $component->display_name,
            'component_key' => $component->key_name,
            'rooms' => $rooms,
        ];

        $hash = $this->hashPayloadCanonical($payload);
        $text = $this->chatGPTService->generateAccommodationGroupComponentReport($payload);

        SurveyAccommodationComponentSummary::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'accommodation_type_id' => $type->id,
                'component_id' => $component->id,
            ],
            [
                'content' => $text,
                'input_hash' => $hash,
            ]
        );

        return [
            'content' => $text,
            'input_hash' => $hash,
        ];
    }

    protected function hashPayloadCanonical(array $payload): string
    {
        return hash('sha256', json_encode($this->sortKeysRecursive($payload)));
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function sortKeysRecursive($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        if (array_values($value) === $value) {
            return array_map([$this, 'sortKeysRecursive'], $value);
        }
        ksort($value);
        $out = [];
        foreach ($value as $k => $v) {
            $out[$k] = $this->sortKeysRecursive($v);
        }

        return $out;
    }

    /**
     * Human-readable label per assessment (e.g. Bedroom 1, Bedroom 2).
     */
    protected function accommodationNumberedDisplayName(SurveyAccommodationAssessment $assessment): string
    {
        $typeName = $assessment->accommodationType->display_name ?? '';
        $cloneIndex = (int) ($assessment->clone_index ?? 0);

        return trim($typeName . ' ' . ($cloneIndex + 1));
    }

    /**
     * Plain-text summary of saved selections for this room (no GPT; used in UI and PDF).
     */
    protected function buildAccommodationSelectionsSummary(SurveyAccommodationAssessment $assessment): string
    {
        $lines = [];
        $lines[] = $this->accommodationNumberedDisplayName($assessment);
        $rating = $assessment->condition_rating;
        $lines[] = 'Condition rating: ' . ($rating !== null ? (string) $rating : 'Not indicated');

        $assessment->loadMissing('location');
        $loc = trim((string) ($assessment->location->value ?? ''));
        if ($loc !== '') {
            $lines[] = 'Location: ' . $loc;
        }

        $notes = trim((string) ($assessment->notes ?? ''));
        if ($notes !== '') {
            $lines[] = 'Additional notes: ' . $notes;
        }

        $lines[] = '';
        $sorted = $assessment->componentAssessments->sortBy(function ($ca) {
            return $ca->component->sort_order ?? 0;
        });

        foreach ($sorted as $compAssessment) {
            $component = $compAssessment->component;
            if (!$component) {
                continue;
            }
            $name = $component->display_name ?? 'Component';
            $material = $compAssessment->material !== null ? (string) ($compAssessment->material->value ?? '') : '';
            $defects = $compAssessment->defects->pluck('value')->filter()->values()->all();

            $lines[] = $name;
            $lines[] = '  Material: ' . ($material !== '' ? $material : '—');
            $lines[] = '  Defects: ' . (!empty($defects) ? implode(', ', $defects) : '—');
            $lines[] = '';
        }

        return trim(implode("\n", $lines));
    }
}

