<?php

namespace Database\Seeders;

use App\Models\SurveySectionDefinition;
use App\Models\SurveySubcategory;
use Illuminate\Database\Seeder;

class SurveySectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get subcategories to attach sections to
        $subcategories = SurveySubcategory::with('category')->get();
        
        if ($subcategories->isEmpty()) {
            $this->command->error('Survey subcategories not found. Please run SurveySubcategorySeeder first.');
            return;
        }

        // Get subcategory mappings
        $roofing = $subcategories->firstWhere('name', 'roofing');
        $walls = $subcategories->firstWhere('name', 'walls');
        $groundworks = $subcategories->firstWhere('name', 'groundworks');
        $windows = $subcategories->firstWhere('name', 'windows');
        $doors = $subcategories->firstWhere('name', 'doors');
        $utilities = $subcategories->firstWhere('name', 'utilities');
        
        $sections = [];

        // Roofing sections
        if ($roofing) {
            $sections[] = [
                'name' => 'roofing_main',
                'display_name' => 'Roofing (Main)',
                'subcategory_id' => $roofing->id,
                'description' => 'Main roof structure, covering, and condition assessment',
                'sort_order' => 1,
                'is_active' => true,
            ];
            $sections[] = [
                'name' => 'chimneys',
                'display_name' => 'Chimneys',
                'subcategory_id' => $roofing->id,
                'description' => 'Chimney stacks, flashings, and pot condition',
                'sort_order' => 2,
                'is_active' => true,
            ];
            $sections[] = [
                'name' => 'roof_drainage',
                'display_name' => 'Roof Drainage',
                'subcategory_id' => $roofing->id,
                'description' => 'Gutters, downpipes, and rainwater drainage',
                'sort_order' => 3,
                'is_active' => true,
            ];
        }

        // Walls sections
        if ($walls) {
            $sections[] = [
                'name' => 'external_walls',
                'display_name' => 'External Walls',
                'subcategory_id' => $walls->id,
                'description' => 'External wall construction and condition',
                'sort_order' => 1,
                'is_active' => true,
            ];
        }

        // Groundworks sections
        if ($groundworks) {
            $sections[] = [
                'name' => 'foundations',
                'display_name' => 'Foundations',
                'subcategory_id' => $groundworks->id,
                'description' => 'Foundation type and condition assessment',
                'sort_order' => 1,
                'is_active' => true,
            ];
        }

        // Windows sections
        if ($windows) {
            $sections[] = [
                'name' => 'windows_external',
                'display_name' => 'Windows',
                'subcategory_id' => $windows->id,
                'description' => 'External windows condition and glazing',
                'sort_order' => 1,
                'is_active' => true,
            ];
        }

        // Doors sections
        if ($doors) {
            $sections[] = [
                'name' => 'external_doors',
                'display_name' => 'External Doors',
                'subcategory_id' => $doors->id,
                'description' => 'External doors condition and security',
                'sort_order' => 1,
                'is_active' => true,
            ];
        }

        // Utilities sections
        if ($utilities) {
            $sections[] = [
                'name' => 'electricity',
                'display_name' => 'Electricity',
                'subcategory_id' => $utilities->id,
                'description' => 'Electrical installation inspection',
                'sort_order' => 1,
                'is_active' => true,
            ];
            $sections[] = [
                'name' => 'gas',
                'display_name' => 'Gas',
                'subcategory_id' => $utilities->id,
                'description' => 'Gas installation and safety',
                'sort_order' => 2,
                'is_active' => true,
            ];
            $sections[] = [
                'name' => 'water_services',
                'display_name' => 'Water Services',
                'subcategory_id' => $utilities->id,
                'description' => 'Water supply and plumbing condition',
                'sort_order' => 3,
                'is_active' => true,
            ];
            $sections[] = [
                'name' => 'heating',
                'display_name' => 'Heating',
                'subcategory_id' => $utilities->id,
                'description' => 'Heating systems and boiler condition',
                'sort_order' => 4,
                'is_active' => true,
            ];
            $sections[] = [
                'name' => 'drainage',
                'display_name' => 'Drainage',
                'subcategory_id' => $utilities->id,
                'description' => 'Drainage systems and inspection',
                'sort_order' => 5,
                'is_active' => true,
            ];
        }

        foreach ($sections as $section) {
            SurveySectionDefinition::updateOrCreate(
                ['name' => $section['name']],
                $section
            );
        }

        $this->command->info('Survey section definitions seeded successfully!');
        $this->command->info('Created ' . count($sections) . ' survey section definitions.');
    }
}
