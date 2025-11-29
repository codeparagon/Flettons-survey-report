<?php

namespace App\Services;

use App\Models\Survey;
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
        if ($useMockData) {
            return $this->getMockAccommodationData($survey);
        }
        
        return $this->getRealAccommodationData($survey);
    }

    /**
     * Get mock accommodation data for UI development.
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getMockAccommodationData(Survey $survey): array
    {
        $components = $this->getAccommodationComponents();
        
        // Get bedroom accommodation type ID (default for mock)
        $bedroomType = SurveyAccommodationType::where('key_name', 'bedroom')->first();
        $accommodationTypeId = $bedroomType ? $bedroomType->id : 1;
        $accommodationTypeName = $bedroomType ? $bedroomType->display_name : 'Bedroom';
        
        return [
            [
                'id' => 'accommodation_1',
                'name' => 'Bedroom 1',
                'accommodation_type_id' => $accommodationTypeId,
                'accommodation_type_name' => $accommodationTypeName,
                'notes' => '', // Shared notes for all components
                'photos' => [], // Shared photos for all components
                'components' => array_map(function($component) {
                    return [
                        'component_key' => $component['key'],
                        'component_name' => $component['name'],
                        'material' => $component['key'] === 'ceiling' ? 'Lath and Plaster' : '',
                        'defects' => $component['key'] === 'ceiling' ? ['No Defects'] : [],
                    ];
                }, $components),
            ],
        ];
    }

    /**
     * Get real accommodation data from database.
     * Falls back to mock data if no assessments exist.
     * 
     * @param Survey $survey
     * @return array
     */
    protected function getRealAccommodationData(Survey $survey): array
    {
        $accommodationAssessments = SurveyAccommodationAssessment::where('survey_id', $survey->id)
            ->with(['accommodationType', 'componentAssessments.component', 'componentAssessments.material', 'componentAssessments.defects', 'photos'])
            ->orderBy('clone_index')
            ->get();
        
        // If no assessments exist, return mock data for UI development
        if ($accommodationAssessments->isEmpty()) {
            return $this->getMockAccommodationData($survey);
        }
        
        return $accommodationAssessments->map(function($assessment) {
            // Map condition rating to string format for display
            $conditionRating = null;
            if ($assessment->condition_rating !== null) {
                $ratingMap = [1 => '1', 2 => '2', 3 => '3'];
                $conditionRating = $ratingMap[$assessment->condition_rating] ?? 'ni';
            } else {
                $conditionRating = 'ni';
            }
            
            return [
                'id' => $assessment->id,
                'name' => $assessment->custom_name ?? $assessment->accommodationType->display_name,
                'accommodation_type_id' => $assessment->accommodation_type_id,
                'accommodation_type_name' => $assessment->accommodationType->display_name ?? '',
                'condition_rating' => $conditionRating,
                'notes' => $assessment->notes ?? '',
                'photos' => $assessment->photos->pluck('id')->toArray(),
                'components' => $assessment->componentAssessments->map(function($compAssessment) {
                    return [
                        'component_key' => $compAssessment->component->key_name,
                        'component_name' => $compAssessment->component->display_name,
                        'material' => $compAssessment->material ? $compAssessment->material->value : '',
                        'defects' => $compAssessment->defects->pluck('value')->toArray(),
                    ];
                })->toArray(),
            ];
        })->toArray();
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
            $accommodationType = SurveyAccommodationType::findOrFail($accommodationTypeId);
            
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
                    $sourceAssessment->custom_name = $formData['custom_name'] ?? $accommodationType->display_name;
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
                // Use custom_name from form if provided, otherwise use source name with clone index
                $assessment->custom_name = !empty($formData['custom_name']) ? $formData['custom_name'] : ($sourceAssessment->custom_name . ' ' . $cloneIndex);
                $assessment->notes = $sourceAssessment->notes; // Copy notes from source
                $assessment->condition_rating = $sourceAssessment->condition_rating; // Copy condition rating from source
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
                $assessment->custom_name = $formData['custom_name'] ?? $assessment->custom_name ?? $accommodationType->display_name;
                $assessment->notes = $formData['notes'] ?? null;
                $assessment->is_completed = true;
                $assessment->completed_at = now();
                $assessment->save();

                // Save components for new/updated assessment
                if (isset($formData['components']) && is_array($formData['components']) && !empty($formData['components'])) {
                    $this->saveAccommodationComponentAssessments($assessment, $formData['components']);
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
            if (!$componentKey) {
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
        
        $maxSortOrder = \App\Models\SurveyAccommodationPhoto::where('accommodation_assessment_id', $assessmentId)
            ->max('sort_order') ?? -1;
        
        foreach ($photos as $index => $photo) {
            if (!$photo->isValid()) {
                continue;
            }
            
            $extension = $photo->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '_' . $index . '.' . $extension;
            $filePath = $photo->storeAs($storagePath, $filename, 'public');
            
            if ($filePath) {
                \App\Models\SurveyAccommodationPhoto::create([
                    'accommodation_assessment_id' => $assessmentId,
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

