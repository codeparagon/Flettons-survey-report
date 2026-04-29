<?php

namespace Tests\Unit;

use App\Models\SurveyAccommodationOption;
use App\Models\SurveyAccommodationOptionType;
use App\Services\SurveyAccommodationDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccommodationLocationDataServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_global_locations_returns_active_global_options(): void
    {
        $type = SurveyAccommodationOptionType::firstOrCreate(
            ['key_name' => 'location'],
            [
                'label' => 'Location',
                'is_multiple' => false,
                'sort_order' => 99,
                'is_active' => true,
            ]
        );

        SurveyAccommodationOption::create([
            'option_type_id' => $type->id,
            'value' => 'Front garden',
            'scope_type' => 'global',
            'scope_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $service = app(SurveyAccommodationDataService::class);
        $locations = $service->getGlobalLocations();

        $this->assertContains('Front garden', $locations);
    }
}
