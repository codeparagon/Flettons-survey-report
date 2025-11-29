<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveyOptionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $optionTypes = [
            [
                'key_name' => 'section_type',
                'label' => 'Section Type',
                'is_multiple' => false,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'key_name' => 'location',
                'label' => 'Location',
                'is_multiple' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'key_name' => 'structure',
                'label' => 'Structure',
                'is_multiple' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'key_name' => 'material',
                'label' => 'Material',
                'is_multiple' => false,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'key_name' => 'defects',
                'label' => 'Defects',
                'is_multiple' => true,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'key_name' => 'remaining_life',
                'label' => 'Remaining Life',
                'is_multiple' => false,
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($optionTypes as $optionType) {
            DB::table('survey_option_types')->updateOrInsert(
                ['key_name' => $optionType['key_name']],
                $optionType
            );
        }

        $this->command->info('Survey option types seeded successfully!');
    }
}


