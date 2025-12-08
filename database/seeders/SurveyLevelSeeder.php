<?php

namespace Database\Seeders;

use App\Models\SurveyLevel;
use App\Models\SurveySectionDefinition;
use Illuminate\Database\Seeder;

class SurveyLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'level_1',
                'display_name' => 'Level 1 - Condition Report',
                'description' => 'Basic building condition report covering essential structural elements.',
                'sort_order' => 1,
                'is_active' => true,
                'sections' => [], // Sections will be added via the Survey Builder
            ],
            [
                'name' => 'level_2',
                'display_name' => 'Level 2 - HomeBuyer Report',
                'description' => 'Comprehensive HomeBuyer survey including all exterior and key interior elements.',
                'sort_order' => 2,
                'is_active' => true,
                'sections' => [], // Sections will be added via the Survey Builder
            ],
            [
                'name' => 'level_3',
                'display_name' => 'Level 3 - Building Survey',
                'description' => 'Full building survey covering all exterior, interior elements and detailed analysis.',
                'sort_order' => 3,
                'is_active' => true,
                'sections' => [], // Sections will be added via the Survey Builder
            ],
        ];

        foreach ($levels as $levelData) {
            $sections = $levelData['sections'];
            unset($levelData['sections']);
            
            $level = SurveyLevel::updateOrCreate(
                ['name' => $levelData['name']],
                $levelData
            );

            // Attach sections to this level if any
            if (!empty($sections)) {
                $sectionIds = SurveySectionDefinition::whereIn('name', $sections)->pluck('id');
                $level->sectionDefinitions()->sync(
                    $sectionIds->mapWithKeys(function ($id, $index) {
                        return [$id => ['sort_order' => $index + 1]];
                    })
                );
            }
        }

        $this->command->info('Survey levels seeded successfully!');
        $this->command->info('Created ' . count($levels) . ' survey levels.');
    }
}
