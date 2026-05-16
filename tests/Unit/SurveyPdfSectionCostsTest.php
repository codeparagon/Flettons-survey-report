<?php

namespace Tests\Unit;

use App\Services\SurveyPdfService;
use Tests\TestCase;

class SurveyPdfSectionCostsTest extends TestCase
{
    public function test_build_section_costs_grouped_for_pdf_merges_child_rows_and_totals(): void
    {
        $service = app(SurveyPdfService::class);

        $section = [
            'costs' => [
                [
                    'category' => 'Provisional works',
                    'description' => 'Repair roof tiles',
                    'due' => '2026',
                    'cost' => '1,500.00',
                ],
            ],
            'child_sections' => [
                [
                    'costs' => [
                        [
                            'category' => 'Provisional works',
                            'description' => 'Replace guttering',
                            'due' => '2027',
                            'cost' => '500.00',
                        ],
                    ],
                ],
            ],
        ];

        $result = $service->buildSectionCostsGroupedForPdf($section);

        $this->assertTrue($result['has_costs']);
        $this->assertArrayHasKey('Provisional works', $result['groups']);
        $this->assertCount(2, $result['groups']['Provisional works']);
        $this->assertSame(2000.0, $result['total']);
        $this->assertSame('£2,000', $service->formatCostAmountForPdf($result['total']));
        $this->assertSame('£1,500', $service->formatStoredCostAmountForPdf('1,500.00'));
    }

    public function test_section_image_anchor_and_child_photos(): void
    {
        $service = app(SurveyPdfService::class);

        $section = [
            'id' => 'merge_acc_comp_5',
            'name' => 'Ceiling',
            'photos' => [],
            'child_sections' => [
                ['photos' => [['file_path' => 'a.jpg']]],
            ],
        ];

        $this->assertTrue($service->sectionHasPhotos($section));
        $this->assertSame(1, $service->sectionPhotoCount($section));
        $this->assertSame('images-section-merge_acc_comp_5', $service->sectionImageAnchorId($section));
    }

    public function test_build_section_costs_grouped_for_pdf_returns_empty_when_no_rows(): void
    {
        $service = app(SurveyPdfService::class);

        $result = $service->buildSectionCostsGroupedForPdf(['costs' => [], 'child_sections' => []]);

        $this->assertFalse($result['has_costs']);
        $this->assertSame([], $result['groups']);
        $this->assertSame(0.0, $result['total']);
    }

    public function test_build_cover_page_data_uses_survey_fields(): void
    {
        $survey = new \App\Models\Survey([
            'full_address' => 'Flat 24, Tenby Court, Tenby Road, London, E17 7AT',
            'level' => 'Level 3',
            'job_reference' => '24E177AT',
            'first_name' => 'Saba',
            'last_name' => 'Al-Shohaty',
            'scheduled_date' => '2025-12-04',
        ]);

        $service = app(SurveyPdfService::class);
        $cover = $service->buildCoverPageData($survey);

        $this->assertSame('Flettons', $cover['company_name']);
        $this->assertSame('LEVEL 3 BUILDING SURVEY REPORT', $cover['level_title']);
        $this->assertSame('Saba Al-Shohaty', $cover['client_name']);
        $this->assertSame('24E177AT', $cover['reference']);
        $this->assertStringContainsString('December', $cover['survey_date']);
        $this->assertFileExists($service->defaultCoverHeroImageAbsolutePath());
    }

    public function test_collect_accommodation_photos_for_pdf_does_not_double_count_component_photos(): void
    {
        $service = app(SurveyPdfService::class);

        $photo = [
            'id' => 42,
            'file_path' => 'accommodation-photos/1/7/ceiling.jpg',
            'file_name' => 'ceiling.jpg',
        ];

        $accommodation = [
            'id' => 7,
            'display_label' => 'Bedroom 1',
            'photos' => [$photo],
            'components' => [
                ['photos' => [$photo]],
            ],
        ];

        $collected = $service->collectAccommodationPhotosForPdf($accommodation);

        $this->assertCount(1, $collected);
        $this->assertSame(42, $collected[0]['id']);
    }
}
