<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyAccommodationOption;
use App\Models\SurveyAccommodationOptionType;
use App\Models\SurveyConditionRatingRule;
use App\Models\SurveyOption;
use App\Models\SurveyOptionType;
use Illuminate\Http\Request;

class ConditionRatingRulesController extends Controller
{
    public function index()
    {
        $optionTypes = ['material', 'defects'];

        // Collect all available values (union):
        // - Survey dropdowns (survey_options)
        // - Accommodation builder options (survey_accommodation_options)
        $allValues = [
            'material' => [],
            'defects' => [],
        ];

        foreach ($optionTypes as $typeKey) {
            $allValues[$typeKey] = $this->collectOptionValues($typeKey);
        }

        // Existing numeric rules (missing => NI)
        $rulesMap = [
            'material' => [],
            'defects' => [],
        ];

        $existingRules = SurveyConditionRatingRule::query()->get(['option_type', 'option_value', 'condition_rating']);
        foreach ($existingRules as $rule) {
            if ($rule->condition_rating === null) {
                continue;
            }
            $rulesMap[$rule->option_type][$rule->option_value] = (int) $rule->condition_rating;
        }

        // Build bins
        $bins = [
            'ni' => [],
            '1' => [],
            '2' => [],
            '3' => [],
        ];

        foreach ($optionTypes as $typeKey) {
            foreach ($allValues[$typeKey] as $normalizedValue => $displayValue) {
                $rating = $rulesMap[$typeKey][$normalizedValue] ?? null;
                $binKey = $rating ? (string) $rating : 'ni';

                $bins[$binKey][] = [
                    'option_type' => $typeKey,
                    'option_value' => $normalizedValue,
                    'display_value' => $displayValue,
                ];
            }
        }

        // Sort chips within each bin for consistent UI
        foreach (['ni', '1', '2', '3'] as $binKey) {
            usort($bins[$binKey], function ($a, $b) {
                return strcmp($a['display_value'], $b['display_value']);
            });
        }

        return view('admin.condition-rating-rules.index', compact('bins'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'material' => 'required|array',
            'material.ni' => 'nullable|array',
            'material.1' => 'nullable|array',
            'material.2' => 'nullable|array',
            'material.3' => 'nullable|array',

            'defects' => 'required|array',
            'defects.ni' => 'nullable|array',
            'defects.1' => 'nullable|array',
            'defects.2' => 'nullable|array',
            'defects.3' => 'nullable|array',
        ]);

        $binsForType = [
            'material' => $validated['material'] ?? [],
            'defects' => $validated['defects'] ?? [],
        ];

        $normalize = function ($value): string {
            return mb_strtolower(trim((string) $value));
        };

        foreach (['material', 'defects'] as $typeKey) {
            $typeBins = $binsForType[$typeKey] ?? [];

            $niValues = array_map($normalize, $typeBins['ni'] ?? []);
            $niValues = array_values(array_filter(array_unique($niValues)));

            if (!empty($niValues)) {
                SurveyConditionRatingRule::query()
                    ->where('option_type', $typeKey)
                    ->whereIn('option_value', $niValues)
                    ->delete();
            }

            $upsertRows = [];
            foreach (['1' => 1, '2' => 2, '3' => 3] as $ratingKey => $ratingValue) {
                $vals = array_map($normalize, $typeBins[$ratingKey] ?? []);
                $vals = array_values(array_filter(array_unique($vals)));

                foreach ($vals as $val) {
                    $upsertRows[] = [
                        'option_type' => $typeKey,
                        'option_value' => $val,
                        'condition_rating' => $ratingValue,
                    ];
                }
            }

            if (!empty($upsertRows)) {
                SurveyConditionRatingRule::query()->upsert(
                    $upsertRows,
                    ['option_type', 'option_value'],
                    ['condition_rating']
                );
            }
        }

        return response()->json(['success' => true]);
    }

    private function collectOptionValues(string $optionTypeKey): array
    {
        $result = [];

        $surveyType = SurveyOptionType::where('key_name', $optionTypeKey)->first();
        if ($surveyType) {
            SurveyOption::query()
                ->where('option_type_id', $surveyType->id)
                ->where('is_active', true)
                ->get(['value'])
                ->each(function (SurveyOption $opt) use (&$result) {
                    $normalized = mb_strtolower(trim((string) $opt->value));
                    if ($normalized === '') {
                        return;
                    }
                    // Keep first encountered display value.
                    if (!array_key_exists($normalized, $result)) {
                        $result[$normalized] = (string) $opt->value;
                    }
                });
        }

        $accommodationType = SurveyAccommodationOptionType::where('key_name', $optionTypeKey)->first();
        if ($accommodationType) {
            SurveyAccommodationOption::query()
                ->where('option_type_id', $accommodationType->id)
                ->where('is_active', true)
                ->get(['value'])
                ->each(function (SurveyAccommodationOption $opt) use (&$result) {
                    $normalized = mb_strtolower(trim((string) $opt->value));
                    if ($normalized === '') {
                        return;
                    }
                    if (!array_key_exists($normalized, $result)) {
                        $result[$normalized] = (string) $opt->value;
                    }
                });
        }

        return $result;
    }
}

