<?php

namespace Database\Seeders;

use App\Models\SurveySection;
use App\Models\SurveyCategory;
use Illuminate\Database\Seeder;

class SurveySectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories exist before creating sections
        $exteriorCategory = SurveyCategory::where('name', 'exterior')->first();
        $interiorCategory = SurveyCategory::where('name', 'interior')->first();
        
        if (!$exteriorCategory || !$interiorCategory) {
            $this->command->error('Survey categories not found. Please run SurveyCategorySeeder first.');
            return;
        }

        $sections = [
            [
                'name' => 'roofs',
                'display_name' => 'Roofs',
                'survey_category_id' => $exteriorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/1.png',
                'description' => 'Roof inspection including tiles, slates, gutters, and drainage',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'walls',
                'display_name' => 'Walls',
                'survey_category_id' => $exteriorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/2.png',
                'description' => 'External wall condition, cracks, damp, and brickwork',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'floors',
                'display_name' => 'Floors',
                'survey_category_id' => $exteriorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/3.png',
                'description' => 'Floor condition, level, joists, and subfloor ventilation',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'doors',
                'display_name' => 'Doors',
                'survey_category_id' => $exteriorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/4.png',
                'description' => 'Door condition, frames, locks, and weatherproofing',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'windows',
                'display_name' => 'Windows',
                'survey_category_id' => $exteriorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/5.png',
                'description' => 'Window condition, glazing, frames, and operation',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'interiors',
                'display_name' => 'Interiors',
                'survey_category_id' => $interiorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/6.png',
                'description' => 'Internal walls, ceilings, plastering, and decorative state',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'utilities',
                'display_name' => 'Utilities',
                'survey_category_id' => $interiorCategory->id,
                'icon' => 'newdesign/assets/vendor/new-survy-icon/7.png',
                'description' => 'Electrical, plumbing, heating, gas, and drainage systems',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($sections as $section) {
            SurveySection::updateOrCreate(
                ['name' => $section['name']],
                $section
            );
        }

        $this->command->info('Survey sections seeded successfully!');
        $this->command->info('Created ' . count($sections) . ' survey sections.');
    }
}