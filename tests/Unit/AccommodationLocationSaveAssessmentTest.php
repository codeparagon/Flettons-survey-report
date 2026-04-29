<?php

namespace Tests\Unit;

use App\Models\Survey;
use App\Models\SurveyAccommodationComponent;
use App\Models\SurveyAccommodationOption;
use App\Models\SurveyAccommodationOptionType;
use App\Models\SurveyAccommodationType;
use App\Services\ChatGPTService;
use App\Services\SurveyAccommodationDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AccommodationLocationSaveAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_save_accommodation_assessment_stores_location_id(): void
    {
        $this->mock(ChatGPTService::class, function ($mock) {
            $mock->shouldReceive('generateAccommodationGroupComponentReport')
                ->zeroOrMoreTimes()
                ->andReturn('Combined narrative');
        });

        $survey = Survey::query()->create([
            'status' => 'in_progress',
        ]);

        $type = SurveyAccommodationType::create([
            'key_name' => 'bedroom_test_loc',
            'display_name' => 'Bedroom',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $component = SurveyAccommodationComponent::create([
            'key_name' => 'ceiling_test_loc',
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
                'notes' => 'Test',
                'location' => 'Front',
                'condition_rating' => 'ni',
                'components' => [[
                    'component_key' => 'ceiling_test_loc',
                    'material' => 'Plaster',
                    'defects' => ['None'],
                ]],
            ],
            false,
            null
        );

        $assessment = $result['assessment'];
        $assessment->refresh();
        $assessment->load('location');

        $this->assertNotNull($assessment->location_id);
        $this->assertSame('Front', $assessment->location->value ?? null);
    }
}
