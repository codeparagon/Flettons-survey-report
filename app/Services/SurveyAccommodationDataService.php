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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                    'name' => $accommodationTypeName, // Always use only the accommodation type name
                    'accommodation_type_id' => $accommodationTypeId,
                    'accommodation_type_name' => $accommodationTypeName,
                    'notes' => '', // Shared notes for all components
                    'photos' => [], // Shared photos for all components
                    'completed_components' => 0,
                    'total_components' => $configuredType->components->count(),
                    'components' => $configuredType->components->map(function($component) {
                        return [
                            'component_key' => $component->key_name,
                            'component_name' => $component->display_name,
                            'material' => '',
                            'defects' => [],
                        ];
                    })->toArray(),
                ],
            ];
        }
        
        // Return empty array if no types with components configured
        return [];
    }

    /**
     * Get real accommodation data from database.
     * Automatically renders sections for all accommodation types with components configured.
     * Returns existing assessments if they exist, otherwise creates default sections for each configured type.
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
                          ->orderBy('survey_accommodation_type_components.sort_order');
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
                                  ->orderBy('survey_accommodation_type_components.sort_order');
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
                $result[] = $this->mapAssessmentToArray($existingAssessment);
            } else {
                // Create default section for this type
                $result[] = [
                    'id' => 'new_' . $typeId, // Temporary ID until saved
                    'name' => $type->display_name,
                    'accommodation_type_id' => $typeId,
                    'accommodation_type_name' => $type->display_name,
                    'condition_rating' => 'ni',
                    'notes' => '',
                    'photos' => [],
                    'report_content' => '',
                    'has_report' => false,
                    'completed_components' => 0,
                    'total_components' => $type->components->count(),
                    'components' => $type->components->map(function($component) {
                        return [
                            'component_key' => $component->key_name,
                            'component_name' => $component->display_name,
                            'material' => '',
                            'defects' => [],
                        ];
                    })->toArray(),
                ];
            }
        }
        
        // Add any clones (assessments with clone_index > 0) after their originals
        foreach ($validAssessments as $assessment) {
            if ($assessment->clone_index > 0) {
                $result[] = $this->mapAssessmentToArray($assessment);
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
     * Map an assessment to the array format expected by the view.
     * 
     * @param SurveyAccommodationAssessment $assessment
     * @return array
     */
    protected function mapAssessmentToArray($assessment): array
    {
        // Map condition rating to string format for display
        $conditionRating = null;
        if ($assessment->condition_rating !== null) {
            $ratingMap = [1 => '1', 2 => '2', 3 => '3'];
            $conditionRating = $ratingMap[$assessment->condition_rating] ?? 'ni';
        } else {
            $conditionRating = 'ni';
        }
        
        // Get accommodation type name - must exist since we filtered for it
        $accommodationTypeName = $assessment->accommodationType->display_name ?? 'Unknown';
        
        // Always use accommodation type name as the display name (not custom_name)
        // The type name should be shown in the title, not "Select Section" or custom names
        $displayName = $accommodationTypeName;
        
        // Get total components from component assessments (only components that were actually added)
        // This ensures we show the correct count based on what was selected when adding the section
        $totalComponents = $assessment->componentAssessments->count();
        
        // Calculate completed components (components with material or defects filled)
        $completedComponents = 0;
        foreach ($assessment->componentAssessments as $compAssessment) {
            $hasMaterial = !empty($compAssessment->material);
            $hasDefects = $compAssessment->defects && $compAssessment->defects->count() > 0;
            if ($hasMaterial || $hasDefects) {
                $completedComponents++;
            }
        }
        
        // Get report content from database (if exists)
        $reportContent = $assessment->report_content ?? '';
        $hasReport = !empty(trim($reportContent));
        
        return [
            'id' => $assessment->id,
            'name' => $displayName, // Always use accommodation type name
            'accommodation_type_id' => $assessment->accommodation_type_id,
            'accommodation_type_name' => $accommodationTypeName,
            'condition_rating' => $conditionRating,
            'notes' => $assessment->notes ?? '',
            'photos' => $assessment->photos ? $assessment->photos->sortBy('sort_order')->map(function($photo) {
                // Generate full URL using asset() helper to include base URL
                $url = asset('storage/' . ltrim($photo->file_path, '/'));
                return [
                    'id' => $photo->id,
                    'file_path' => $photo->file_path,
                    'file_name' => $photo->file_name,
                    'url' => $url,
                ];
            })->values()->toArray() : [],
            'report_content' => $reportContent,
            'has_report' => $hasReport,
            'completed_components' => $completedComponents,
            'total_components' => $totalComponents,
            'components' => $assessment->componentAssessments->map(function($compAssessment) {
                return [
                    'component_key' => $compAssessment->component->key_name,
                    'component_name' => $compAssessment->component->display_name,
                    'material' => $compAssessment->material ? $compAssessment->material->value : '',
                    'defects' => $compAssessment->defects->pluck('value')->toArray(),
                ];
            })->toArray(),
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
     * Get material options for a specific component type from database.
     * 
     * @param string $componentKey
     * @return array
     */
    public function getComponentMaterials(string $componentKey): array
    {
        $component = SurveyAccommodationComponent::where('key_name', $componentKey)->first();
        if (!$component) {
            return [];
        }

        $materialType = SurveyAccommodationOptionType::where('key_name', 'material')->first();
        if (!$materialType) {
            return [];
        }

        return SurveyAccommodationOption::where('option_type_id', $materialType->id)
            ->where('scope_type', 'component')
            ->where('scope_id', $component->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Get defect options for accommodation components from database.
     * 
     * @return array
     */
    public function getComponentDefects(): array
    {
        $defectType = SurveyAccommodationOptionType::where('key_name', 'defects')->first();
        if (!$defectType) {
            return [];
        }

        return SurveyAccommodationOption::where('option_type_id', $defectType->id)
            ->where('scope_type', 'global')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('value')
            ->toArray();
    }

    /**
     * Save accommodation assessment and generate report.
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
                        ->with(['componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'photos'])
                        ->first();
                }
                
                // If no specific source, find the original (clone_index = 0)
                if (!$sourceAssessment) {
                    $sourceAssessment = SurveyAccommodationAssessment::where('survey_id', $survey->id)
                        ->where('accommodation_type_id', $accommodationTypeId)
                        ->where('clone_index', 0)
                        ->with(['componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'photos'])
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

            $assessment->load(['accommodationType', 'componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects']);
            
            $chatGPTData = $this->prepareAccommodationChatGPTData($assessment, $formData);
            
            $reportContent = '';
            try {
                $accommodationName = $assessment->custom_name ?? $accommodationType->display_name;
                $reportContent = $this->chatGPTService->generateAccommodationReport($chatGPTData, $accommodationName);
                
                // Save report content to database
                $assessment->report_content = $reportContent;
                $assessment->save();
            } catch (\Exception $e) {
                Log::error('Failed to generate accommodation report content', [
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

            $componentAssessment->save();

            if (isset($componentData['defects']) && is_array($componentData['defects'])) {
                $defectOptionIds = $this->findAccommodationDefectOptionIds($defectType->id, $componentData['defects']);
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
     */
    protected function findAccommodationDefectOptionIds(int $defectOptionTypeId, array $defectValues): array
    {
        if (empty($defectValues)) {
            return [];
        }

        return SurveyAccommodationOption::where('option_type_id', $defectOptionTypeId)
            ->whereIn('value', $defectValues)
            ->where('is_active', true)
            ->where('scope_type', 'global')
            ->pluck('id')
            ->toArray();
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
     * Prepare data for ChatGPT service (accommodation).
     */
    protected function prepareAccommodationChatGPTData(SurveyAccommodationAssessment $assessment, array $formData): array
    {
        $components = [];
        
        foreach ($assessment->componentAssessments as $compAssessment) {
            $components[] = [
                'component' => $compAssessment->component->display_name ?? '',
                'material' => $compAssessment->material->value ?? '',
                'defects' => $compAssessment->defects->pluck('value')->toArray(),
            ];
        }

        return [
            'accommodation_name' => $assessment->custom_name ?? $assessment->accommodationType->display_name ?? '',
            'accommodation_type' => $assessment->accommodationType->display_name ?? '',
            'components' => $components,
            'notes' => $assessment->notes ?? $formData['notes'] ?? '',
        ];
    }
}

