<?php

namespace Database\Seeders;

use App\Models\SurveyLevel;
use App\Models\SurveySection;
use Illuminate\Database\Seeder;

class SurveyLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Level 1',
                'display_name' => 'Basic Survey',
                'description' => 'Essential building inspection covering basic structural elements.',
                'sort_order' => 1,
                'is_active' => true,
                'sections' => ['roofs', 'walls', 'floors'],
            ],
            [
                'name' => 'Level 2',
                'display_name' => 'Standard Survey',
                'description' => 'Comprehensive building inspection including all exterior elements.',
                'sort_order' => 2,
                'is_active' => true,
                'sections' => ['roofs', 'walls', 'floors', 'doors', 'windows'],
            ],
            [
                'name' => 'Level 3',
                'display_name' => 'Complete Survey',
                'description' => 'Full building inspection covering exterior and basic interior elements.',
                'sort_order' => 3,
                'is_active' => true,
                'sections' => ['roofs', 'walls', 'floors', 'doors', 'windows', 'interiors'],
            ],
            [
                'name' => 'Level 4',
                'display_name' => 'Premium Survey',
                'description' => 'Comprehensive building inspection covering all exterior and interior elements.',
                'sort_order' => 4,
                'is_active' => true,
                'sections' => ['roofs', 'walls', 'floors', 'doors', 'windows', 'interiors', 'utilities'],
            ],
        ];

        foreach ($levels as $levelData) {
            $sections = $levelData['sections'];
            unset($levelData['sections']);
            
            $level = SurveyLevel::updateOrCreate(
                ['name' => $levelData['name']],
                $levelData
            );

            // Attach sections to this level
            $sectionIds = SurveySection::whereIn('name', $sections)->pluck('id');
            $level->sections()->sync(
                $sectionIds->mapWithKeys(function ($id, $index) {
                    return [$id => ['sort_order' => $index + 1]];
                })
            );
        }

        $this->command->info('Survey levels seeded successfully!');
        $this->command->info('Created ' . count($levels) . ' survey levels with their sections.');
    }
}