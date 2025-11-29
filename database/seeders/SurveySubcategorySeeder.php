<?php

namespace Database\Seeders;

use App\Models\SurveyCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveySubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $exteriorCategory = SurveyCategory::where('name', 'exterior')->first();
        $interiorCategory = SurveyCategory::where('name', 'interior')->first();
        
        if (!$exteriorCategory || !$interiorCategory) {
            $this->command->error('Survey categories not found. Please run SurveyCategorySeeder first.');
            return;
        }

        $subcategories = [
            // Exterior subcategories
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'roofing',
                'display_name' => 'Roofing',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'chimneys',
                'display_name' => 'Chimneys, Pots and Stacks',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'walls',
                'display_name' => 'Walls',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'windows',
                'display_name' => 'Windows',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'doors',
                'display_name' => 'Doors',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'gutters',
                'display_name' => 'Gutters and Drainage',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'category_id' => $exteriorCategory->id,
                'name' => 'foundations',
                'display_name' => 'Foundations',
                'sort_order' => 7,
                'is_active' => true,
            ],
            // Interior subcategories
            [
                'category_id' => $interiorCategory->id,
                'name' => 'floors',
                'display_name' => 'Floors',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $interiorCategory->id,
                'name' => 'walls',
                'display_name' => 'Walls',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => $interiorCategory->id,
                'name' => 'ceilings',
                'display_name' => 'Ceilings',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'category_id' => $interiorCategory->id,
                'name' => 'utilities',
                'display_name' => 'Utilities',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($subcategories as $subcategory) {
            DB::table('survey_subcategories')->updateOrInsert(
                ['category_id' => $subcategory['category_id'], 'name' => $subcategory['name']],
                $subcategory
            );
        }

        $this->command->info('Survey subcategories seeded successfully!');
        $this->command->info('Created ' . count($subcategories) . ' survey subcategories.');
    }
}

