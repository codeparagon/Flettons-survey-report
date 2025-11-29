<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveySectionDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        // Get subcategories
        $subcategories = DB::table('survey_subcategories')->get()->keyBy('name');
        
        if ($subcategories->isEmpty()) {
            $this->command->error('Survey subcategories not found. Please run SurveySubcategorySeeder first.');
            return;
        }

        // Get categories to distinguish exterior/interior walls and floors
        $categories = DB::table('survey_categories')->get()->keyBy('name');
        $exteriorCategoryId = $categories['exterior']->id ?? null;
        $interiorCategoryId = $categories['interior']->id ?? null;
        
        // Get exterior and interior subcategories separately
        $exteriorWalls = $subcategories->first(function($sub) use ($exteriorCategoryId) {
            return $sub->name === 'walls' && $sub->category_id == $exteriorCategoryId;
        });
        $interiorFloors = $subcategories->first(function($sub) use ($interiorCategoryId) {
            return $sub->name === 'floors' && $sub->category_id == $interiorCategoryId;
        });

        $sectionDefinitions = [
            // Roofing sections
            [
                'subcategory_id' => $subcategories['roofing']->id ?? null,
                'name' => 'roofing',
                'display_name' => 'Roofing',
                'is_clonable' => true,
                'max_clones' => 10,
                'sort_order' => 1,
                'is_active' => true,
            ],
            // Chimneys sections
            [
                'subcategory_id' => $subcategories['chimneys']->id ?? null,
                'name' => 'chimneys',
                'display_name' => 'Chimneys, Pots and Stacks',
                'is_clonable' => true,
                'max_clones' => 5,
                'sort_order' => 1,
                'is_active' => true,
            ],
            // Walls sections (Exterior)
            [
                'subcategory_id' => $exteriorWalls->id ?? null,
                'name' => 'walls',
                'display_name' => 'Walls',
                'is_clonable' => true,
                'max_clones' => 10,
                'sort_order' => 1,
                'is_active' => true,
            ],
            // Windows sections
            [
                'subcategory_id' => $subcategories['windows']->id ?? null,
                'name' => 'windows',
                'display_name' => 'Windows',
                'is_clonable' => true,
                'max_clones' => 20,
                'sort_order' => 1,
                'is_active' => true,
            ],
            // Doors sections
            [
                'subcategory_id' => $subcategories['doors']->id ?? null,
                'name' => 'doors',
                'display_name' => 'Doors',
                'is_clonable' => true,
                'max_clones' => 10,
                'sort_order' => 1,
                'is_active' => true,
            ],
            // Floors sections (Interior)
            [
                'subcategory_id' => $interiorFloors->id ?? null,
                'name' => 'floors',
                'display_name' => 'Floors',
                'is_clonable' => true,
                'max_clones' => 10,
                'sort_order' => 1,
                'is_active' => true,
            ],
            // Utilities sections
            [
                'subcategory_id' => $subcategories['utilities']->id ?? null,
                'name' => 'utilities',
                'display_name' => 'Utilities',
                'is_clonable' => false,
                'max_clones' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        // Filter out null subcategory_ids
        $sectionDefinitions = array_filter($sectionDefinitions, function($def) {
            return $def['subcategory_id'] !== null;
        });

        foreach ($sectionDefinitions as $definition) {
            DB::table('survey_section_definitions')->updateOrInsert(
                ['subcategory_id' => $definition['subcategory_id'], 'name' => $definition['name']],
                $definition
            );
        }

        $this->command->info('Survey section definitions seeded successfully!');
        $this->command->info('Created ' . count($sectionDefinitions) . ' section definitions.');
    }
}

