<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveySectionRequiredFieldsSeeder extends Seeder
{
    public function run(): void
    {
        // Get option types
        $optionTypes = DB::table('survey_option_types')->get()->keyBy('key_name');
        
        if ($optionTypes->isEmpty()) {
            $this->command->error('Survey option types not found. Please run SurveyOptionTypeSeeder first.');
            return;
        }

        // Get all section definitions
        $sectionDefinitions = DB::table('survey_section_definitions')->get();
        
        if ($sectionDefinitions->isEmpty()) {
            $this->command->error('Survey section definitions not found. Please run SurveySectionDefinitionSeeder first.');
            return;
        }

        // Define required fields for each section definition
        // Most sections require: section_type, location, structure, material, defects, remaining_life
        $requiredFields = [
            'section_type' => $optionTypes['section_type']->id ?? null,
            'location' => $optionTypes['location']->id ?? null,
            'structure' => $optionTypes['structure']->id ?? null,
            'material' => $optionTypes['material']->id ?? null,
            'defects' => $optionTypes['defects']->id ?? null,
            'remaining_life' => $optionTypes['remaining_life']->id ?? null,
        ];

        // Filter out null option types
        $requiredFields = array_filter($requiredFields, function($id) {
            return $id !== null;
        });

        foreach ($sectionDefinitions as $sectionDef) {
            foreach ($requiredFields as $optionTypeId) {
                DB::table('survey_section_required_fields')->updateOrInsert(
                    [
                        'section_definition_id' => $sectionDef->id,
                        'option_type_id' => $optionTypeId,
                    ],
                    [
                        'section_definition_id' => $sectionDef->id,
                        'option_type_id' => $optionTypeId,
                    ]
                );
            }
        }

        $this->command->info('Survey section required fields seeded successfully!');
    }
}


