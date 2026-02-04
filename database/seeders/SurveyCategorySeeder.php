<?php

namespace Database\Seeders;

use App\Models\SurveyCategory;
use Illuminate\Database\Seeder;

class SurveyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'exterior',
                'display_name' => 'Exterior',
                'icon' => 'fa fa-home',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'interior',
                'display_name' => 'Interior',
                'icon' => 'fa fa-door-open',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            SurveyCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Survey categories seeded successfully!');
        $this->command->info('Created ' . count($categories) . ' survey categories.');
    }
}