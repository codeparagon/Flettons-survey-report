<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyLevel;
use App\Models\SurveyContentSection;
use App\Models\SurveyCategory;
use App\Models\SurveySubcategory;
use Barryvdh\DomPDF\Facade\Pdf;

class SurveyPdfService
{
    protected SurveyDataService $surveyDataService;
    protected SurveyAccommodationDataService $accommodationDataService;

    public function __construct(
        SurveyDataService $surveyDataService,
        SurveyAccommodationDataService $accommodationDataService
    ) {
        $this->surveyDataService = $surveyDataService;
        $this->accommodationDataService = $accommodationDataService;
    }

    /**
     * Collect all section data for PDF generation.
     * 
     * @param Survey $survey
     * @return array
     */
    public function collectAllSectionData(Survey $survey): array
    {
        // Get all regular sections with their data
        $categories = $this->surveyDataService->getGroupedSurveyData($survey, false);
        
        // All room rows with saved assessment data (PDF table — no empty placeholders)
        $accommodationSections = $this->accommodationDataService->getAccommodationRowsForPdf($survey);
        
        // Get all content sections directly from database (to ensure latest content)
        $contentSections = $this->getContentSectionsForSurvey($survey, $categories);
        
        // Collect all survey images (with global numbering for [Photos n–m] references)
        $surveyImages = $this->collectAllSurveyImages($categories, $accommodationSections);
        $surveyImages = $this->assignGlobalPhotoNumbersToSurveyImages($surveyImages);
        $accommodationTableRows = $this->buildAccommodationPdfTableRows($accommodationSections, $surveyImages);

        return [
            'categories' => $categories,
            'accommodationSections' => $accommodationSections,
            'accommodationTableRows' => $accommodationTableRows,
            'contentSections' => $contentSections,
            'surveyImages' => $surveyImages,
        ];
    }

    /**
     * Collect all images from sections and accommodations for PDF.
     * 
     * @param array $categories
     * @param array $accommodationSections
     * @return array
     */
    protected function collectAllSurveyImages(array $categories, array $accommodationSections): array
    {
        $images = [];

        foreach ($categories as $subCategories) {
            foreach ($subCategories as $sections) {
                foreach ($sections as $section) {
                    if (! is_array($section)) {
                        continue;
                    }
                    $photos = $this->collectSectionPhotos($section);
                    if ($photos === []) {
                        continue;
                    }

                    $images[] = [
                        'type' => 'section',
                        'id' => $section['id'] ?? null,
                        'anchor_id' => $this->sectionImageAnchorId($section),
                        'name' => $section['name'] ?? 'Unknown Section',
                        'photos' => $photos,
                    ];
                }
            }
        }

        foreach ($accommodationSections as $accommodation) {
            if (! is_array($accommodation)) {
                continue;
            }
            $allPhotos = $this->collectAccommodationPhotosForPdf($accommodation);
            if ($allPhotos === []) {
                continue;
            }

            $images[] = [
                'type' => 'accommodation',
                'id' => $accommodation['id'] ?? null,
                'anchor_id' => $this->accommodationImageAnchorId($accommodation),
                'name' => $accommodation['display_label'] ?? $accommodation['name'] ?? ($accommodation['accommodation_type_name'] ?? 'Unknown Accommodation'),
                'photos' => $allPhotos,
            ];
        }

        return $images;
    }

    /**
     * Room-level accommodation photos for PDF (assessment photos only — not merged again from components).
     *
     * @param  array<string, mixed>  $accommodation
     * @return list<array<string, mixed>>
     */
    public function collectAccommodationPhotosForPdf(array $accommodation): array
    {
        $photos = [];
        if (! empty($accommodation['photos']) && is_array($accommodation['photos'])) {
            $photos = $accommodation['photos'];
        }

        return $this->deduplicatePhotosForPdf($photos);
    }

    /**
     * @param  list<array<string, mixed>>  $photos
     * @return list<array<string, mixed>>
     */
    protected function deduplicatePhotosForPdf(array $photos): array
    {
        $seenIds = [];
        $seenBasenames = [];
        $out = [];

        foreach ($photos as $photo) {
            if (! is_array($photo)) {
                continue;
            }

            $id = $photo['id'] ?? null;
            if ($id !== null && $id !== '') {
                $idKey = (string) $id;
                if (isset($seenIds[$idKey])) {
                    continue;
                }
                $seenIds[$idKey] = true;
            }

            $basename = basename(ltrim((string) ($photo['file_path'] ?? ''), '/'));
            if ($basename !== '') {
                if (isset($seenBasenames[$basename])) {
                    continue;
                }
                $seenBasenames[$basename] = true;
            }

            $out[] = $photo;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $section
     * @return list<array<string, mixed>>
     */
    public function collectSectionPhotos(array $section): array
    {
        $photos = [];
        if (! empty($section['photos']) && is_array($section['photos'])) {
            $photos = array_merge($photos, $section['photos']);
        }
        foreach ($section['child_sections'] ?? [] as $child) {
            if (is_array($child) && ! empty($child['photos']) && is_array($child['photos'])) {
                $photos = array_merge($photos, $child['photos']);
            }
        }

        return $photos;
    }

    public function sectionHasPhotos(array $section): bool
    {
        return $this->collectSectionPhotos($section) !== [];
    }

    public function sectionPhotoCount(array $section): int
    {
        return count($this->collectSectionPhotos($section));
    }

    public function sectionAnchorId(array $section): string
    {
        return 'section-'.$this->sanitizePdfAnchorToken((string) ($section['id'] ?? md5((string) ($section['name'] ?? 'section'))));
    }

    public function sectionImageAnchorId(array $section): string
    {
        return 'images-section-'.$this->sanitizePdfAnchorToken((string) ($section['id'] ?? md5((string) ($section['name'] ?? 'section'))));
    }

    /**
     * @param  array<string, mixed>  $accommodation
     */
    public function accommodationImageAnchorId(array $accommodation): string
    {
        $token = (string) ($accommodation['id'] ?? md5((string) ($accommodation['name'] ?? 'accommodation')));

        return 'images-accommodation-'.$this->sanitizePdfAnchorToken($token);
    }

    public function categoryAnchorId(string $categoryName): string
    {
        return 'category-'.$this->sanitizePdfAnchorToken($categoryName);
    }

    public function subcategoryAnchorId(string $categoryName, string $subCategoryName): string
    {
        return 'subcategory-'.md5($categoryName.'|'.$subCategoryName);
    }

    public function accommodationConfigAnchorId(): string
    {
        return 'accommodation-config';
    }

    public function contentSectionAnchorId(object $contentSection): string
    {
        $id = $contentSection->id ?? null;

        return $id
            ? 'content-section-'.$this->sanitizePdfAnchorToken((string) $id)
            : 'content-section-'.md5((string) ($contentSection->title ?? 'content'));
    }

    protected function sanitizePdfAnchorToken(string $token): string
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_-]+/', '', $token);

        return $sanitized !== '' ? $sanitized : md5($token);
    }

    /**
     * Absolute filesystem path for DomPDF <img src="...">.
     *
     * @param  array<string, mixed>  $photo
     */
    public function resolvePhotoAbsolutePathForPdf(array $photo): ?string
    {
        $filePath = ltrim((string) ($photo['file_path'] ?? ''), '/');
        if ($filePath === '') {
            return null;
        }

        $candidates = [
            storage_path('app/public/'.$filePath),
            public_path('storage/'.$filePath),
        ];

        foreach ($candidates as $path) {
            $resolved = realpath($path);
            if ($resolved !== false && is_file($resolved)) {
                return str_replace('\\', '/', $resolved);
            }
        }

        return null;
    }

    /**
     * Sequential photo numbers across all image groups (for accommodation table references).
     *
     * @param  list<array<string, mixed>>  $surveyImages
     * @return list<array<string, mixed>>
     */
    protected function assignGlobalPhotoNumbersToSurveyImages(array $surveyImages): array
    {
        $n = 1;
        foreach ($surveyImages as $gi => $group) {
            $photos = $group['photos'] ?? [];
            if (! is_array($photos)) {
                continue;
            }
            foreach ($photos as $pi => $photo) {
                if (! is_array($photo)) {
                    continue;
                }
                $surveyImages[$gi]['photos'][$pi]['pdf_number'] = $n;
                $n++;
            }
        }

        return $surveyImages;
    }

    /**
     * Rows for the Configuration of Accommodation PDF table (matches Flettons report layout).
     *
     * @param  list<array<string, mixed>>  $accommodationSections
     * @param  list<array<string, mixed>>  $surveyImages
     * @return list<array{room: string, location: string, position: string, observations: string, photo_ref: string}>
     */
    public function buildAccommodationPdfTableRows(array $accommodationSections, array $surveyImages): array
    {
        $photoNumbersByAccId = [];
        foreach ($surveyImages as $group) {
            if (($group['type'] ?? '') !== 'accommodation') {
                continue;
            }
            $id = $group['id'] ?? null;
            if ($id === null || $id === '') {
                continue;
            }
            $nums = [];
            foreach ($group['photos'] ?? [] as $photo) {
                if (isset($photo['pdf_number'])) {
                    $nums[] = (int) $photo['pdf_number'];
                }
            }
            if ($nums !== []) {
                sort($nums);
                $photoNumbersByAccId[(string) $id] = $nums;
            }
        }

        $rows = [];
        foreach ($accommodationSections as $accommodation) {
            if (! is_array($accommodation)) {
                continue;
            }
            $accId = (string) ($accommodation['id'] ?? '');
            [$locationCol, $positionCol] = $this->splitAccommodationLocationForPdf(
                (string) ($accommodation['location'] ?? '')
            );

            $photoNums = $photoNumbersByAccId[$accId] ?? [];
            $rows[] = [
                'room' => (string) ($accommodation['display_label'] ?? $accommodation['name'] ?? ($accommodation['accommodation_type_name'] ?? '')),
                'location' => $locationCol,
                'position' => $positionCol,
                'observations' => $this->buildAccommodationPdfObservationsText($accommodation),
                'photo_ref' => $this->formatAccommodationPhotoReference($photoNums),
                'photo_anchor_id' => $photoNums !== [] ? $this->accommodationImageAnchorId($accommodation) : '',
            ];
        }

        return $rows;
    }

    /**
     * @return array{0: string, 1: string} [location column, front/rear/center column]
     */
    protected function splitAccommodationLocationForPdf(string $location): array
    {
        $loc = trim($location);
        if ($loc === '') {
            return ['', ''];
        }

        if (preg_match('/^(front|rear|centre|center|middle)$/i', $loc)) {
            $pos = strtolower($loc);
            if ($pos === 'center') {
                $pos = 'centre';
            }

            return ['', ucfirst($pos)];
        }

        if (preg_match('/floor/i', $loc)) {
            return [$loc, ''];
        }

        return [$loc, ''];
    }

    /**
     * Narrative for the "Photos and Observations" column (component GPT bullets, then general observations).
     */
    protected function buildAccommodationPdfObservationsText(array $accommodation): string
    {
        $paragraphs = [];

        foreach ($accommodation['components'] ?? [] as $component) {
            if (! is_array($component)) {
                continue;
            }
            foreach ($this->normalizeGptObservationLines($component['gpt_observations'] ?? null) as $line) {
                $paragraphs[] = $line;
            }
        }

        foreach ($this->normalizeGptObservationLines($accommodation['gpt_observations'] ?? null) as $line) {
            $paragraphs[] = $line;
        }

        if ($paragraphs !== []) {
            return implode("\n\n", $paragraphs);
        }

        $report = trim((string) ($accommodation['report_content'] ?? ''));
        if ($report !== '') {
            return $report;
        }

        $fromComponents = $this->buildAccommodationPdfObservationsFromComponentFields($accommodation);
        if ($fromComponents !== '') {
            return $fromComponents;
        }

        $notes = trim((string) ($accommodation['notes'] ?? ''));
        if ($notes !== '') {
            return $notes;
        }

        return trim((string) ($accommodation['gpt_narrative'] ?? ''));
    }

    /**
     * @return list<string>
     */
    protected function normalizeGptObservationLines(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $raw = $decoded;
            } else {
                $t = trim($raw);

                return $t !== '' ? [$t] : [];
            }
        }
        if (! is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $line) {
            $t = trim((string) $line);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return $out;
    }

    /**
     * Plain-language paragraphs from saved materials / defects / notes when GPT text is absent.
     */
    protected function buildAccommodationPdfObservationsFromComponentFields(array $accommodation): string
    {
        $paragraphs = [];

        foreach ($accommodation['components'] ?? [] as $component) {
            if (! is_array($component)) {
                continue;
            }
            $name = trim((string) ($component['component_name'] ?? 'Component'));
            $material = trim((string) ($component['material'] ?? ''));
            $defects = is_array($component['defects'] ?? null) ? $component['defects'] : [];
            $defects = array_values(array_filter(array_map('strval', $defects), static fn ($d) => trim($d) !== '' && strcasecmp($d, 'No Defects') !== 0));
            $addNotes = trim((string) ($component['additional_notes'] ?? ''));

            $sentences = [];
            if ($material !== '') {
                $sentences[] = 'The '.$name.' are of '.$material.' construction.';
            }
            if ($defects !== []) {
                $sentences[] = 'Defects noted include '.implode(', ', $defects).'.';
            }
            if ($addNotes !== '') {
                $sentences[] = $addNotes;
            }

            if ($sentences !== []) {
                $paragraphs[] = implode(' ', $sentences);
            }
        }

        return $paragraphs !== [] ? implode("\n\n", $paragraphs) : '';
    }

    /**
     * Report body for a section row in the PDF (merged accommodation groups + standard sections).
     *
     * @param  array<string, mixed>  $section
     */
    public function resolveSectionPdfReportContent(array $section): string
    {
        if (! empty($section['merged_accommodation_component_group'])) {
            $merged = trim((string) ($section['merged_report_content'] ?? ''));
            if ($merged !== '') {
                return $merged;
            }
            $blocks = [];
            foreach ($section['child_sections'] ?? [] as $child) {
                if (! is_array($child)) {
                    continue;
                }
                $rc = trim((string) ($child['report_content'] ?? ''));
                if ($rc !== '') {
                    $label = trim((string) ($child['merged_acc_room_label'] ?? $child['name'] ?? ''));
                    $blocks[] = ($label !== '' ? $label."\n\n" : '').$rc;
                }
            }

            return implode("\n\n---\n\n", $blocks);
        }

        $report = trim((string) ($section['report_content'] ?? ''));

        return $report;
    }

    /**
     * Whether the section has any displayable PDF content beyond the title.
     *
     * @param  array<string, mixed>  $section
     */
    public function sectionHasPdfDisplayContent(array $section): bool
    {
        if (trim($this->resolveSectionPdfReportContent($section)) !== '') {
            return true;
        }

        if (! empty($section['material']) || ! empty($section['structure'])) {
            return true;
        }
        if (! empty($section['notes'])) {
            return true;
        }
        $defects = $section['defects'] ?? [];
        if (is_array($defects) && count($defects) > 0) {
            return true;
        }
        $opts = $section['option_selections'] ?? [];
        if (is_array($opts)) {
            foreach (['material', 'structure', 'location', 'defects', 'remaining_life'] as $key) {
                $v = $opts[$key] ?? null;
                if (is_array($v) && count($v) > 0) {
                    return true;
                }
                if (is_string($v) && trim($v) !== '') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Flat list of cost rows for a section (includes merged accommodation child rows).
     *
     * @param  array<string, mixed>  $section
     * @return list<array<string, mixed>>
     */
    public function collectCostsFromSection(array $section): array
    {
        $all = [];
        if (! empty($section['costs']) && is_array($section['costs'])) {
            $all = array_merge($all, $section['costs']);
        }
        foreach ($section['child_sections'] ?? [] as $child) {
            if (! is_array($child)) {
                continue;
            }
            if (! empty($child['costs']) && is_array($child['costs'])) {
                $all = array_merge($all, $child['costs']);
            }
        }

        return $all;
    }

    /**
     * Costs grouped by category for the PDF table (Description of Works / Due / Estimated Cost).
     *
     * @param  array<string, mixed>  $section
     * @return array{has_costs: bool, groups: array<string, list<array<string, mixed>>>, total: float}
     */
    public function buildSectionCostsGroupedForPdf(array $section): array
    {
        $rows = $this->collectCostsFromSection($section);
        if ($rows === []) {
            return ['has_costs' => false, 'groups' => [], 'total' => 0.0];
        }

        $groups = [];
        foreach ($rows as $cost) {
            if (! is_array($cost)) {
                continue;
            }
            $category = trim((string) ($cost['category'] ?? ''));
            if ($category === '') {
                $category = 'Other';
            }
            if (! isset($groups[$category])) {
                $groups[$category] = [];
            }
            $groups[$category][] = $cost;
        }

        $total = 0.0;
        foreach ($rows as $cost) {
            if (is_array($cost)) {
                $total += $this->parseCostAmount($cost['cost'] ?? $cost['amount'] ?? 0);
            }
        }

        return ['has_costs' => true, 'groups' => $groups, 'total' => $total];
    }

    public function formatCostAmountForPdf(float $amount): string
    {
        return '£'.number_format($amount, 0, '.', ',');
    }

    public function formatStoredCostAmountForPdf(mixed $cost): string
    {
        return $this->formatCostAmountForPdf($this->parseCostAmount($cost));
    }

    protected function parseCostAmount(mixed $cost): float
    {
        if (is_numeric($cost)) {
            return (float) $cost;
        }
        $s = preg_replace('/[^0-9.]/', '', (string) $cost);

        return $s !== '' ? (float) $s : 0.0;
    }

    /**
     * Cover page view data (Flettons Level 3 layout).
     *
     * @param  list<array<string, mixed>>  $surveyImages
     * @return array<string, mixed>
     */
    public function buildCoverPageData(Survey $survey, array $surveyImages = []): array
    {
        return [
            'company_name' => 'Flettons',
            'level_title' => $this->formatCoverLevelTitle($survey->level),
            'address_lines' => $this->formatCoverAddressLines($survey),
            'client_name' => $survey->client_name,
            'survey_date' => $this->formatCoverSurveyDate($survey->scheduled_date),
            'reference' => trim((string) ($survey->job_reference ?? '')) !== ''
                ? (string) $survey->job_reference
                : 'N/A',
            'hero_image_path' => $this->resolveCoverHeroImageAbsolutePath(),
            'disclaimer' => 'We are acting on your written instructions as confirmed by our Building Survey Terms and Conditions',
        ];
    }

    public function formatCoverLevelTitle(?string $level): string
    {
        $level = trim((string) $level);
        if ($level === '') {
            return 'BUILDING SURVEY REPORT';
        }
        if (preg_match('/(\d+)/', $level, $matches)) {
            return 'LEVEL '.$matches[1].' BUILDING SURVEY REPORT';
        }

        return strtoupper($level).' BUILDING SURVEY REPORT';
    }

    /**
     * @return list<string>
     */
    public function formatCoverAddressLines(Survey $survey): array
    {
        $address = trim((string) ($survey->full_address ?? ''));
        if ($address === '') {
            $address = trim((string) $survey->property_address_full);
        }
        if ($address === '' || $address === 'Address not provided') {
            return ['Property Address Not Provided'];
        }

        $parts = array_values(array_filter(array_map('trim', preg_split('/,\s*/', $address))));
        if ($parts === []) {
            return [$address];
        }
        if (count($parts) <= 3) {
            return $parts;
        }

        $lines = [implode(', ', array_slice($parts, 0, -2))];

        return array_merge($lines, array_slice($parts, -2));
    }

    public function formatCoverSurveyDate(mixed $date): string
    {
        try {
            $dt = $date ? \Carbon\Carbon::parse($date) : now();
        } catch (\Throwable $e) {
            $dt = now();
        }

        return $dt->format('l jS F Y');
    }

    /**
     * Branded UK stock image for the PDF cover (not desk-study maps or survey photos).
     */
    public function resolveCoverHeroImageAbsolutePath(): string
    {
        return $this->defaultCoverHeroImageAbsolutePath();
    }

    public function defaultCoverHeroImageAbsolutePath(): string
    {
        $path = public_path('images/pdf-cover-default.jpg');
        $resolved = is_file($path) ? realpath($path) : false;

        return str_replace('\\', '/', $resolved !== false ? $resolved : $path);
    }

    public function resolveStorageRelativePathForPdf(string $relativePath): ?string
    {
        return $this->resolvePhotoAbsolutePathForPdf(['file_path' => $relativePath]);
    }

    /**
     * @param  list<int>  $numbers
     */
    protected function formatAccommodationPhotoReference(array $numbers): string
    {
        if ($numbers === []) {
            return '';
        }
        $min = min($numbers);
        $max = max($numbers);
        if ($min === $max) {
            return '[Photos '.$min.']';
        }

        return '[Photos '.$min.' - '.$max.']';
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
     * Get content sections for a survey, grouped by their link type.
     * This matches the controller's logic but fetches fresh data.
     * 
     * @param Survey $survey
     * @param array $categories
     * @return array
     */
    protected function getContentSectionsForSurvey(Survey $survey, array $categories): array
    {
        $contentSections = [
            'standalone' => [],
            'by_category' => [],
            'by_subcategory' => [],
        ];

        // Get content sections based on survey level
        // If survey has no level set (null/empty), show all sections for backward compatibility
        // If survey has a level set, only show sections assigned to that level
        
        if (empty($survey->level)) {
            // No level set - show all active content sections (backward compatibility for old surveys)
            $allContentSections = SurveyContentSection::active()
                ->ordered()
                ->with(['category', 'subcategory'])
                ->get();
        } else {
            // Level is set - only show sections assigned to this level
            $surveyLevel = $this->findSurveyLevelByValue($survey->level);
            
            if (!$surveyLevel) {
                // Level doesn't exist in database - return empty
                $allContentSections = collect();
            } else {
                // Level exists - get assigned content sections
                $contentSectionIds = $surveyLevel->contentSections()->pluck('survey_content_sections.id')->unique();
                
                if ($contentSectionIds->isEmpty()) {
                    // Level exists but has no content sections assigned - return empty
                    $allContentSections = collect();
                } else {
                    // Level exists and has content sections - return only those sections
                    $allContentSections = SurveyContentSection::whereIn('id', $contentSectionIds)
                        ->active()
                        ->ordered()
                        ->with(['category', 'subcategory'])
                        ->get();
                }
            }
        }

        // Get survey level name to determine which categories/subcategories are relevant
        $surveyLevelName = $survey->level ?? null;
        $relevantCategoryIds = [];
        $relevantSubcategoryIds = [];

        // Extract category and subcategory IDs from the categories array
        foreach ($categories as $categoryName => $subCategories) {
            foreach ($subCategories as $subCategoryName => $sections) {
                // Try to find the actual category/subcategory from database
                $category = SurveyCategory::where('display_name', $categoryName)->first();
                $subcategory = SurveySubcategory::where('display_name', $subCategoryName)->first();
                
                if ($category) {
                    $relevantCategoryIds[] = $category->id;
                }
                if ($subcategory) {
                    $relevantSubcategoryIds[] = $subcategory->id;
                }
            }
        }

        foreach ($allContentSections as $contentSection) {
            if ($contentSection->subcategory_id) {
                // Subcategory-linked: add to by_subcategory if it matches
                if (in_array($contentSection->subcategory_id, $relevantSubcategoryIds)) {
                    $subcategory = $contentSection->subcategory;
                    $category = $subcategory->category ?? null;
                    if ($category) {
                        $categoryName = $category->display_name;
                        $subcategoryName = $subcategory->display_name;
                        if (!isset($contentSections['by_subcategory'][$categoryName])) {
                            $contentSections['by_subcategory'][$categoryName] = [];
                        }
                        if (!isset($contentSections['by_subcategory'][$categoryName][$subcategoryName])) {
                            $contentSections['by_subcategory'][$categoryName][$subcategoryName] = [];
                        }
                        $contentSections['by_subcategory'][$categoryName][$subcategoryName][] = $contentSection;
                    }
                }
            } elseif ($contentSection->category_id) {
                // Category-linked: add to by_category if it matches
                if (in_array($contentSection->category_id, $relevantCategoryIds)) {
                    $category = $contentSection->category;
                    $categoryName = $category->display_name;
                    if (!isset($contentSections['by_category'][$categoryName])) {
                        $contentSections['by_category'][$categoryName] = [];
                    }
                    $contentSections['by_category'][$categoryName][] = $contentSection;
                }
            } else {
                // Standalone: add to standalone array
                $contentSections['standalone'][] = $contentSection;
            }
        }

        return $contentSections;
    }

    /**
     * Generate PDF from survey data.
     * 
     * @param Survey $survey
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePdf(Survey $survey)
    {
        // Collect all section data
        $data = $this->collectAllSectionData($survey);
        
        // Load the survey with relationships for PDF view
        $survey->load('surveyor');
        
        // Generate PDF using the view
        $pdf = Pdf::loadView('surveyor.surveys.pdf.report', [
            'survey' => $survey,
            'categories' => $data['categories'],
            'accommodationSections' => $data['accommodationSections'],
            'accommodationTableRows' => $data['accommodationTableRows'] ?? [],
            'contentSections' => $data['contentSections'],
            'surveyImages' => $data['surveyImages'],
            'coverPage' => $this->buildCoverPageData($survey, $data['surveyImages']),
            'pdfService' => $this,
        ]);
        
        // Set PDF options for UK A4 format
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isJavascriptEnabled', false);
        $pdf->setOption('defaultFont', 'dejavu sans');

        return $pdf;
    }
}
