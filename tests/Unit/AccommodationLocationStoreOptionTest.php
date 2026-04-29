<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\AccommodationBuilderController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AccommodationLocationStoreOptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_option_persists_global_location_value(): void
    {
        $controller = app(AccommodationBuilderController::class);
        $request = Request::create('/admin/api/accommodation-options', 'POST', [
            'option_type' => 'location',
            'value' => 'Rear elevation',
        ]);

        $response = $controller->storeOption($request);

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertTrue($payload['success'] ?? false);

        $this->assertDatabaseHas('survey_accommodation_options', [
            'value' => 'Rear elevation',
        ]);
    }
}
