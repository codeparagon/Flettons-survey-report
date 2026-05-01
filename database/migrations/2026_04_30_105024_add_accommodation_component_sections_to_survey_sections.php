<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a real regular "Accommodation Components" sub-category + one SurveySectionDefinition per
        // accommodation component so it appears alongside other non-accommodation sections.
        //
        // These sections are intentionally lightweight: option types = material, location, defects.

        $categoryId = DB::table('survey_categories')
            ->whereIn('name', ['interior', 'exterior'])
            ->orderByRaw("FIELD(name, 'interior', 'exterior')")
            ->value('id');

        if (!$categoryId) {
            return;
        }

        // 1) Ensure subcategory exists.
        $subKey = 'accommodation_components';
        $subId = DB::table('survey_subcategories')
            ->where('category_id', $categoryId)
            ->where('name', $subKey)
            ->value('id');

        if (!$subId) {
            $nextSort = (int) (DB::table('survey_subcategories')->where('category_id', $categoryId)->max('sort_order') ?? 0) + 1;
            $subId = (int) DB::table('survey_subcategories')->insertGetId([
                'category_id' => $categoryId,
                'name' => $subKey,
                'display_name' => 'Accommodation Components',
                'sort_order' => $nextSort,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('survey_subcategories')->where('id', $subId)->update([
                'display_name' => 'Accommodation Components',
                'is_active' => true,
                'updated_at' => now(),
            ]);
        }

        // 2) Insert/update one section definition per accommodation component.
        $components = DB::table('survey_accommodation_components')
            ->select(['id', 'key_name', 'display_name'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($components->isEmpty()) {
            return;
        }

        $existing = DB::table('survey_section_definitions')
            ->where('subcategory_id', $subId)
            ->pluck('id', 'name'); // name => id

        $createdSectionDefIds = [];
        $sort = 1;
        foreach ($components as $c) {
            $name = 'acc_component__' . (string) $c->key_name;
            $displayName = (string) ($c->display_name ?: $c->key_name);

            if ($existing->has($name)) {
                $id = (int) $existing->get($name);
                DB::table('survey_section_definitions')->where('id', $id)->update([
                    'display_name' => $displayName,
                    'is_clonable' => true,
                    'max_clones' => null,
                    'sort_order' => $sort,
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
                $createdSectionDefIds[] = $id;
            } else {
                $id = (int) DB::table('survey_section_definitions')->insertGetId([
                    'subcategory_id' => $subId,
                    'name' => $name,
                    'display_name' => $displayName,
                    'is_clonable' => true,
                    'max_clones' => null,
                    'sort_order' => $sort,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdSectionDefIds[] = $id;
            }
            $sort++;
        }

        if (empty($createdSectionDefIds)) {
            return;
        }

        // 3) Required fields: material, location, defects only.
        $otIds = DB::table('survey_option_types')
            ->whereIn('key_name', ['material', 'location', 'defects'])
            ->pluck('id', 'key_name');

        foreach ($createdSectionDefIds as $sid) {
            foreach (['material', 'location', 'defects'] as $key) {
                $otId = $otIds[$key] ?? null;
                if (!$otId) continue;
                DB::table('survey_section_required_fields')->updateOrInsert(
                    ['section_definition_id' => $sid, 'option_type_id' => $otId],
                    [
                        'section_definition_id' => $sid,
                        'option_type_id' => $otId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 4) Ensure these new sections appear for level-based surveys too (assign to all levels).
        $levels = DB::table('survey_levels')->select(['id'])->get();
        foreach ($levels as $lvl) {
            $baseSort = (int) (DB::table('survey_level_section_definitions')->where('survey_level_id', $lvl->id)->max('sort_order') ?? 0) + 10;
            $i = 0;
            foreach ($createdSectionDefIds as $sid) {
                DB::table('survey_level_section_definitions')->updateOrInsert(
                    ['survey_level_id' => $lvl->id, 'section_definition_id' => $sid],
                    [
                        'survey_level_id' => $lvl->id,
                        'section_definition_id' => $sid,
                        'sort_order' => $baseSort + $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $i++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $subId = DB::table('survey_subcategories')
            ->where('name', 'accommodation_components')
            ->value('id');

        if (!$subId) {
            return;
        }

        $sectionDefIds = DB::table('survey_section_definitions')
            ->where('subcategory_id', $subId)
            ->where('name', 'like', 'acc_component__%')
            ->pluck('id')
            ->all();

        if (!empty($sectionDefIds)) {
            DB::table('survey_level_section_definitions')->whereIn('section_definition_id', $sectionDefIds)->delete();
            DB::table('survey_section_required_fields')->whereIn('section_definition_id', $sectionDefIds)->delete();
            DB::table('survey_section_definitions')->whereIn('id', $sectionDefIds)->delete();
        }

        DB::table('survey_subcategories')->where('id', $subId)->delete();
    }
};
