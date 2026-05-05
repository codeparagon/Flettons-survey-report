<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ensures every Survey Level includes links to all "Accommodation Components" section definitions.
 *
 * Levels created after the original migration would otherwise miss these rows in
 * survey_level_section_definitions, so those surveys would never show Ceiling/Walls/etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        $subId = DB::table('survey_subcategories')->where('name', 'accommodation_components')->value('id');
        if (!$subId) {
            return;
        }

        $sectionDefIds = DB::table('survey_section_definitions')
            ->where('subcategory_id', $subId)
            ->where('name', 'like', 'acc_component__%')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('id')
            ->all();

        if ($sectionDefIds === []) {
            return;
        }

        $levels = DB::table('survey_levels')->select('id')->get();

        foreach ($levels as $lvl) {
            $lvlId = (int) $lvl->id;
            $baseSort = (int) (DB::table('survey_level_section_definitions')->where('survey_level_id', $lvlId)->max('sort_order') ?? 0);
            $i = 0;

            foreach ($sectionDefIds as $sid) {
                $sid = (int) $sid;
                DB::table('survey_level_section_definitions')->updateOrInsert(
                    ['survey_level_id' => $lvlId, 'section_definition_id' => $sid],
                    [
                        'survey_level_id' => $lvlId,
                        'section_definition_id' => $sid,
                        'sort_order' => $baseSort + 10 + $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $i++;
            }
        }
    }

    public function down(): void
    {
        $subId = DB::table('survey_subcategories')->where('name', 'accommodation_components')->value('id');
        if (!$subId) {
            return;
        }

        $sectionDefIds = DB::table('survey_section_definitions')
            ->where('subcategory_id', $subId)
            ->where('name', 'like', 'acc_component__%')
            ->pluck('id')
            ->all();

        if ($sectionDefIds === []) {
            return;
        }

        DB::table('survey_level_section_definitions')->whereIn('section_definition_id', $sectionDefIds)->delete();
    }
};
