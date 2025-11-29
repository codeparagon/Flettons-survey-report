<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SurveyCategorySeeder::class,
            SurveySubcategorySeeder::class,
            SurveyOptionTypeSeeder::class,
            SurveySectionDefinitionSeeder::class,
            SurveyOptionSeeder::class,
            SurveySectionRequiredFieldsSeeder::class,
            AccommodationSeeder::class,
            // SurveyLevelSeeder::class, // Commented out - survey_levels table removed in restructure
            SurveySeeder::class,
        ]);
    }
}

