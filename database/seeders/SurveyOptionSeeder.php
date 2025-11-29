<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveyOptionSeeder extends Seeder
{
    public function run(): void
    {
        // Get option types
        $optionTypes = DB::table('survey_option_types')->get()->keyBy('key_name');
        $categories = DB::table('survey_categories')->get()->keyBy('name');
        $subcategories = DB::table('survey_subcategories')->get()->keyBy('name');
        
        if ($optionTypes->isEmpty()) {
            $this->command->error('Survey option types not found. Please run SurveyOptionTypeSeeder first.');
            return;
        }

        $options = [];

        // Section Type Options (scoped to subcategory)
        if (isset($optionTypes['section_type'])) {
            $roofingSubcat = $subcategories['roofing'] ?? null;
            if ($roofingSubcat) {
                $options[] = ['option_type_id' => $optionTypes['section_type']->id, 'value' => 'Main Roof', 'scope_type' => 'subcategory', 'scope_id' => $roofingSubcat->id, 'sort_order' => 1];
                $options[] = ['option_type_id' => $optionTypes['section_type']->id, 'value' => 'Side Extension', 'scope_type' => 'subcategory', 'scope_id' => $roofingSubcat->id, 'sort_order' => 2];
                $options[] = ['option_type_id' => $optionTypes['section_type']->id, 'value' => 'Rear Extension', 'scope_type' => 'subcategory', 'scope_id' => $roofingSubcat->id, 'sort_order' => 3];
                $options[] = ['option_type_id' => $optionTypes['section_type']->id, 'value' => 'Dormer', 'scope_type' => 'subcategory', 'scope_id' => $roofingSubcat->id, 'sort_order' => 4];
                $options[] = ['option_type_id' => $optionTypes['section_type']->id, 'value' => 'Lean-to', 'scope_type' => 'subcategory', 'scope_id' => $roofingSubcat->id, 'sort_order' => 5];
            }
        }

        // Location Options (global)
        if (isset($optionTypes['location'])) {
            $options[] = ['option_type_id' => $optionTypes['location']->id, 'value' => 'Whole Property', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 1];
            $options[] = ['option_type_id' => $optionTypes['location']->id, 'value' => 'Right', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 2];
            $options[] = ['option_type_id' => $optionTypes['location']->id, 'value' => 'Left', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 3];
            $options[] = ['option_type_id' => $optionTypes['location']->id, 'value' => 'Front', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 4];
            $options[] = ['option_type_id' => $optionTypes['location']->id, 'value' => 'Rear', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 5];
        }

        // Structure Options (scoped to category)
        if (isset($optionTypes['structure'])) {
            $exteriorCategory = $categories['exterior'] ?? null;
            $interiorCategory = $categories['interior'] ?? null;
            
            if ($exteriorCategory) {
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Pitched', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 1];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Flat', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 2];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Inverted pitched', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 3];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Mono-Pitch', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 4];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Curved', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 5];
            }
            
            if ($interiorCategory) {
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Standard', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 1];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Flat', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 2];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Pitched', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 3];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Suspended', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 4];
                $options[] = ['option_type_id' => $optionTypes['structure']->id, 'value' => 'Solid', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 5];
            }
        }

        // Material Options (scoped to category)
        if (isset($optionTypes['material'])) {
            $exteriorCategory = $categories['exterior'] ?? null;
            $interiorCategory = $categories['interior'] ?? null;
            
            if ($exteriorCategory) {
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Double Glazed Aluminium', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 1];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Polycarbonate', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 2];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Slate', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 3];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Asphalt', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 4];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Concrete Interlocking', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 5];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Fibre Slate', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 6];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Felt', 'scope_type' => 'category', 'scope_id' => $exteriorCategory->id, 'sort_order' => 7];
            }
            
            if ($interiorCategory) {
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Plasterboard', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 1];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Plaster', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 2];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Timber', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 3];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Concrete', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 4];
                $options[] = ['option_type_id' => $optionTypes['material']->id, 'value' => 'Mixed', 'scope_type' => 'category', 'scope_id' => $interiorCategory->id, 'sort_order' => 5];
            }
        }

        // Defect Options (global)
        if (isset($optionTypes['defects'])) {
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'None', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 1];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Holes', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 2];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Perished', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 3];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Thermal Sag', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 4];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Deflection', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 5];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Rot', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 6];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Moss', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 7];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Lichen', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 8];
            $options[] = ['option_type_id' => $optionTypes['defects']->id, 'value' => 'Slipped Tiles', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 9];
        }

        // Remaining Life Options (global)
        if (isset($optionTypes['remaining_life'])) {
            $options[] = ['option_type_id' => $optionTypes['remaining_life']->id, 'value' => '0', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 1];
            $options[] = ['option_type_id' => $optionTypes['remaining_life']->id, 'value' => '1-5', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 2];
            $options[] = ['option_type_id' => $optionTypes['remaining_life']->id, 'value' => '6-10', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 3];
            $options[] = ['option_type_id' => $optionTypes['remaining_life']->id, 'value' => '10+', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 4];
        }

        foreach ($options as $option) {
            DB::table('survey_options')->updateOrInsert(
                [
                    'option_type_id' => $option['option_type_id'],
                    'value' => $option['value'],
                    'scope_type' => $option['scope_type'],
                    'scope_id' => $option['scope_id'],
                ],
                array_merge($option, ['is_active' => true])
            );
        }

        $this->command->info('Survey options seeded successfully!');
        $this->command->info('Created ' . count($options) . ' survey options.');
    }
}


