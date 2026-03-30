<?php

namespace Tests\Unit;

use App\Services\SurveyDataService;
use Tests\TestCase;

class SurveyDataServiceNormalizeOptionsTest extends TestCase
{
    public function test_normalize_merges_flat_legacy_keys_into_options(): void
    {
        $service = app(SurveyDataService::class);

        $out = $service->normalizeOptionsFromFormData([
            'section' => 'Main Roof',
            'location' => 'Front',
            'structure' => 'Pitched',
            'material' => 'Slate',
            'remaining_life' => '10',
            'defects' => ['None'],
        ]);

        $this->assertSame('Main Roof', $out['section_type']);
        $this->assertSame('Front', $out['location']);
        $this->assertSame('Pitched', $out['structure']);
        $this->assertSame('Slate', $out['material']);
        $this->assertSame('10', $out['remaining_life']);
        $this->assertSame(['None'], $out['defects']);
    }

    public function test_normalize_nested_options_array_is_merged_with_flat_keys(): void
    {
        $service = app(SurveyDataService::class);

        $out = $service->normalizeOptionsFromFormData([
            'section' => 'A',
            'options' => [
                'pros' => 'Good light',
                'location' => 'Rear',
            ],
        ]);

        $this->assertSame('A', $out['section_type']);
        $this->assertSame('Rear', $out['location']);
        $this->assertSame('Good light', $out['pros']);
    }
}
