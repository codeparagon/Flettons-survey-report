<?php

namespace Tests\Unit;

use App\Models\Survey;
use App\Models\SurveyAccommodationAssessment;
use App\Models\SurveyAccommodationComponent;
use App\Models\SurveyAccommodationGptOutput;
use App\Models\SurveyAccommodationOption;
use App\Models\SurveyAccommodationOptionType;
use App\Models\SurveyAccommodationType;
use App\Services\ChatGPTService;
use App\Services\SurveyAccommodationDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AccommodationCombinedGptTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_regenerate_combined_gpt_excludes_empty_room_and_persists(): void
    {
        $capturedPayload = null;

        $this->mock(ChatGPTService::class, function ($mock) use (&$capturedPayload) {
            $mock->shouldReceive('generateAccommodationCombinedReport')
                ->once()
                ->andReturnUsing(function (array $payload) use (&$capturedPayload) {
                    $capturedPayload = $payload;

                    return [
                        'narrative' => 'Narrative text',
                        'observations' => ['Bullet one', 'Bullet two'],
                        'component_observations' => [
                            'wall_gpt' => ['Wall note'],
                        ],
                    ];
                });
            $mock->shouldReceive('generateAccommodationRoomComponentObservations')
                ->once()
                ->andReturn([
                    'component_observations' => [
                        'wall_gpt' => ['Wall note'],
                    ],
                ]);
        });

        $survey = Survey::query()->create(['status' => 'in_progress']);

        $type = SurveyAccommodationType::create([
            'key_name' => 'bedroom_gpt',
            'display_name' => 'Bedroom',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $component = SurveyAccommodationComponent::create([
            'key_name' => 'wall_gpt',
            'display_name' => 'Walls',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        DB::table('survey_accommodation_type_components')->insert([
            'accommodation_type_id' => $type->id,
            'component_id' => $component->id,
            'is_required' => false,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $materialType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'material'],
            ['label' => 'Material', 'is_multiple' => false, 'sort_order' => 1, 'is_active' => true]
        );
        $defectType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'defects'],
            ['label' => 'Defects', 'is_multiple' => true, 'sort_order' => 2, 'is_active' => true]
        );

        SurveyAccommodationOption::create([
            'option_type_id' => $materialType->id,
            'value' => 'Brick',
            'scope_type' => 'component',
            'scope_id' => $component->id,
            'sort_order' => 0,
            'is_active' => true,
        ]);
        SurveyAccommodationOption::create([
            'option_type_id' => $defectType->id,
            'value' => 'None',
            'scope_type' => 'global',
            'scope_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        SurveyAccommodationAssessment::create([
            'survey_id' => $survey->id,
            'accommodation_type_id' => $type->id,
            'clone_index' => 0,
            'custom_name' => $type->display_name,
            'notes' => null,
            'location_id' => null,
            'condition_rating' => null,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        SurveyAccommodationAssessment::create([
            'survey_id' => $survey->id,
            'accommodation_type_id' => $type->id,
            'clone_index' => 1,
            'custom_name' => $type->display_name,
            'notes' => null,
            'location_id' => null,
            'condition_rating' => null,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $service = app(SurveyAccommodationDataService::class);

        $materialOpt = SurveyAccommodationOption::where('option_type_id', $materialType->id)
            ->where('scope_id', $component->id)
            ->firstOrFail();
        $noneOpt = SurveyAccommodationOption::where('option_type_id', $defectType->id)
            ->where('value', 'None')
            ->firstOrFail();

        $filled = SurveyAccommodationAssessment::where('survey_id', $survey->id)
            ->where('clone_index', 0)
            ->firstOrFail();

        DB::table('survey_accommodation_component_assessments')->insert([
            'accommodation_assessment_id' => $filled->id,
            'component_id' => $component->id,
            'material_id' => $materialOpt->id,
            'location_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $caRow = DB::table('survey_accommodation_component_assessments')
            ->where('accommodation_assessment_id', $filled->id)
            ->first();

        DB::table('survey_accommodation_component_defects')->insert([
            'component_assessment_id' => $caRow->id,
            'defect_option_id' => $noneOpt->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = $service->regenerateAccommodationTypeCombinedGpt($survey, $type->id);

        $this->assertSame('Narrative text', $result['gpt_narrative']);
        $this->assertSame(['Bullet one', 'Bullet two'], $result['gpt_observations']);
        $this->assertNull($result['gpt_generation_error']);

        $this->assertNotNull($capturedPayload);
        $this->assertCount(1, $capturedPayload['rooms']);

        $row = SurveyAccommodationGptOutput::where('survey_id', $survey->id)
            ->where('accommodation_type_id', $type->id)
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('Narrative text', $row->narrative);
        $this->assertSame(['Bullet one', 'Bullet two'], $row->observations);

        $filledCa = \App\Models\SurveyAccommodationComponentAssessment::query()
            ->where('accommodation_assessment_id', $filled->id)
            ->where('component_id', $component->id)
            ->first();
        $this->assertNotNull($filledCa);
        $this->assertSame(['Wall note'], $filledCa->gpt_observations ?? []);

        $emptyRoom = SurveyAccommodationAssessment::where('survey_id', $survey->id)
            ->where('clone_index', 1)
            ->firstOrFail();
        $emptyCa = \App\Models\SurveyAccommodationComponentAssessment::query()
            ->where('accommodation_assessment_id', $emptyRoom->id)
            ->where('component_id', $component->id)
            ->first();
        $this->assertNull($emptyCa);
    }

    public function test_save_accommodation_assessment_returns_gpt_fields(): void
    {
        $this->mock(ChatGPTService::class, function ($mock) {
            $mock->shouldReceive('generateAccommodationCombinedReport')
                ->zeroOrMoreTimes()
                ->andReturn([
                    'narrative' => 'Saved narrative',
                    'observations' => ['Obs'],
                    'component_observations' => [
                        'ceiling_save_gpt' => ['Ceiling obs'],
                    ],
                ]);
            $mock->shouldReceive('generateAccommodationRoomComponentObservations')
                ->zeroOrMoreTimes()
                ->andReturn([
                    'component_observations' => [
                        'ceiling_save_gpt' => ['Ceiling obs'],
                    ],
                ]);
        });

        $survey = Survey::query()->create(['status' => 'in_progress']);

        $type = SurveyAccommodationType::create([
            'key_name' => 'bedroom_save_gpt',
            'display_name' => 'Bedroom',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $component = SurveyAccommodationComponent::create([
            'key_name' => 'ceiling_save_gpt',
            'display_name' => 'Ceiling',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        DB::table('survey_accommodation_type_components')->insert([
            'accommodation_type_id' => $type->id,
            'component_id' => $component->id,
            'is_required' => false,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $materialType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'material'],
            ['label' => 'Material', 'is_multiple' => false, 'sort_order' => 1, 'is_active' => true]
        );
        $defectType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'defects'],
            ['label' => 'Defects', 'is_multiple' => true, 'sort_order' => 2, 'is_active' => true]
        );
        $locationType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'location'],
            ['label' => 'Location', 'is_multiple' => false, 'sort_order' => 3, 'is_active' => true]
        );

        SurveyAccommodationOption::create([
            'option_type_id' => $materialType->id,
            'value' => 'Plaster',
            'scope_type' => 'component',
            'scope_id' => $component->id,
            'sort_order' => 0,
            'is_active' => true,
        ]);
        SurveyAccommodationOption::create([
            'option_type_id' => $defectType->id,
            'value' => 'None',
            'scope_type' => 'global',
            'scope_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);
        SurveyAccommodationOption::create([
            'option_type_id' => $locationType->id,
            'value' => 'Front',
            'scope_type' => 'global',
            'scope_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $service = app(SurveyAccommodationDataService::class);

        $result = $service->saveAccommodationAssessment(
            $survey,
            $type->id,
            [
                'notes' => 'Note',
                'location' => 'Front',
                'condition_rating' => 'ni',
                'components' => [[
                    'component_key' => 'ceiling_save_gpt',
                    'material' => 'Plaster',
                    'defects' => ['None'],
                ]],
            ],
            false,
            null
        );

        $this->assertArrayHasKey('gpt_narrative', $result);
        $this->assertArrayHasKey('gpt_observations', $result);
        $this->assertArrayHasKey('gpt_generation_error', $result);
        $this->assertSame('Saved narrative', $result['gpt_narrative']);
        $this->assertSame(['Obs'], $result['gpt_observations']);
        $this->assertSame(['ceiling_save_gpt' => ['Ceiling obs']], $result['gpt_component_observations']);

        $assessment = $result['assessment'];
        $ca = \App\Models\SurveyAccommodationComponentAssessment::query()
            ->where('accommodation_assessment_id', $assessment->id)
            ->whereHas('component', fn ($q) => $q->where('key_name', 'ceiling_save_gpt'))
            ->first();
        $this->assertNotNull($ca);
        $this->assertSame(['Ceiling obs'], $ca->gpt_observations ?? []);
    }

    public function test_regenerate_scoped_to_one_room_preserves_other_room_gpt_observations(): void
    {
        $this->mock(ChatGPTService::class, function ($mock) {
            $mock->shouldReceive('generateAccommodationCombinedReport')
                ->once()
                ->andReturnUsing(function (array $payload) {
                    $this->assertCount(2, $payload['rooms'] ?? []);

                    return [
                        'narrative' => 'Merged narrative for two rooms',
                        'observations' => ['Type-level obs'],
                        'component_observations' => [
                            'wall_scope_gpt' => ['Cross-room wall'],
                        ],
                    ];
                });
            $mock->shouldReceive('generateAccommodationRoomComponentObservations')
                ->once()
                ->andReturn([
                    'component_observations' => [
                        'wall_scope_gpt' => ['Room two only'],
                    ],
                ]);
        });

        $survey = Survey::query()->create(['status' => 'in_progress']);

        $type = SurveyAccommodationType::create([
            'key_name' => 'bedroom_scope_gpt',
            'display_name' => 'Bedroom',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $component = SurveyAccommodationComponent::create([
            'key_name' => 'wall_scope_gpt',
            'display_name' => 'Walls',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        DB::table('survey_accommodation_type_components')->insert([
            'accommodation_type_id' => $type->id,
            'component_id' => $component->id,
            'is_required' => false,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $materialType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'material'],
            ['label' => 'Material', 'is_multiple' => false, 'sort_order' => 1, 'is_active' => true]
        );
        $defectType = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'defects'],
            ['label' => 'Defects', 'is_multiple' => true, 'sort_order' => 2, 'is_active' => true]
        );

        SurveyAccommodationOption::create([
            'option_type_id' => $materialType->id,
            'value' => 'Brick',
            'scope_type' => 'component',
            'scope_id' => $component->id,
            'sort_order' => 0,
            'is_active' => true,
        ]);
        SurveyAccommodationOption::create([
            'option_type_id' => $defectType->id,
            'value' => 'None',
            'scope_type' => 'global',
            'scope_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $room1 = SurveyAccommodationAssessment::create([
            'survey_id' => $survey->id,
            'accommodation_type_id' => $type->id,
            'clone_index' => 0,
            'custom_name' => $type->display_name,
            'notes' => null,
            'location_id' => null,
            'condition_rating' => null,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $room2 = SurveyAccommodationAssessment::create([
            'survey_id' => $survey->id,
            'accommodation_type_id' => $type->id,
            'clone_index' => 1,
            'custom_name' => $type->display_name,
            'notes' => null,
            'location_id' => null,
            'condition_rating' => null,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $materialOpt = SurveyAccommodationOption::where('option_type_id', $materialType->id)
            ->where('scope_id', $component->id)
            ->firstOrFail();
        $noneOpt = SurveyAccommodationOption::where('option_type_id', $defectType->id)
            ->where('value', 'None')
            ->firstOrFail();

        foreach ([$room1, $room2] as $room) {
            DB::table('survey_accommodation_component_assessments')->insert([
                'accommodation_assessment_id' => $room->id,
                'component_id' => $component->id,
                'material_id' => $materialOpt->id,
                'location_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $caRow = DB::table('survey_accommodation_component_assessments')
                ->where('accommodation_assessment_id', $room->id)
                ->first();
            DB::table('survey_accommodation_component_defects')->insert([
                'component_assessment_id' => $caRow->id,
                'defect_option_id' => $noneOpt->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $caRoom1 = \App\Models\SurveyAccommodationComponentAssessment::query()
            ->where('accommodation_assessment_id', $room1->id)
            ->where('component_id', $component->id)
            ->firstOrFail();
        $caRoom1->gpt_observations = ['Frozen room 1'];
        $caRoom1->save();

        $service = app(SurveyAccommodationDataService::class);
        $service->regenerateAccommodationTypeCombinedGpt($survey, $type->id, [], $room2->id);

        $caRoom1->refresh();
        $this->assertSame(['Frozen room 1'], $caRoom1->gpt_observations ?? []);

        $caRoom2 = \App\Models\SurveyAccommodationComponentAssessment::query()
            ->where('accommodation_assessment_id', $room2->id)
            ->where('component_id', $component->id)
            ->firstOrFail();
        $this->assertSame(['Room two only'], $caRoom2->gpt_observations ?? []);

        $gptRow = SurveyAccommodationGptOutput::where('survey_id', $survey->id)
            ->where('accommodation_type_id', $type->id)
            ->firstOrFail();
        $this->assertSame('Merged narrative for two rooms', $gptRow->narrative);
    }
}
