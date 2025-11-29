<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        // Accommodation Types
        $accommodationTypes = [
            ['key_name' => 'bedroom', 'display_name' => 'Bedroom', 'sort_order' => 1, 'is_active' => true],
            ['key_name' => 'bathroom', 'display_name' => 'Bathroom', 'sort_order' => 2, 'is_active' => true],
            ['key_name' => 'kitchen', 'display_name' => 'Kitchen', 'sort_order' => 3, 'is_active' => true],
            ['key_name' => 'living_room', 'display_name' => 'Living Room', 'sort_order' => 4, 'is_active' => true],
        ];

        foreach ($accommodationTypes as $type) {
            DB::table('survey_accommodation_types')->updateOrInsert(
                ['key_name' => $type['key_name']],
                $type
            );
        }

        // Accommodation Components
        $components = [
            ['key_name' => 'ceiling', 'display_name' => 'Ceiling', 'sort_order' => 1, 'is_active' => true],
            ['key_name' => 'walls', 'display_name' => 'Walls', 'sort_order' => 2, 'is_active' => true],
            ['key_name' => 'windows', 'display_name' => 'Windows', 'sort_order' => 3, 'is_active' => true],
            ['key_name' => 'internal_door', 'display_name' => 'Internal Door', 'sort_order' => 4, 'is_active' => true],
            ['key_name' => 'external_door', 'display_name' => 'External Door', 'sort_order' => 5, 'is_active' => true],
            ['key_name' => 'floors', 'display_name' => 'Floors', 'sort_order' => 6, 'is_active' => true],
            ['key_name' => 'services', 'display_name' => 'Services', 'sort_order' => 7, 'is_active' => true],
        ];

        foreach ($components as $component) {
            DB::table('survey_accommodation_components')->updateOrInsert(
                ['key_name' => $component['key_name']],
                $component
            );
        }

        // Accommodation Option Types
        $optionTypes = [
            ['key_name' => 'material', 'label' => 'Material', 'is_multiple' => false, 'sort_order' => 1, 'is_active' => true],
            ['key_name' => 'defects', 'label' => 'Defects', 'is_multiple' => true, 'sort_order' => 2, 'is_active' => true],
        ];

        foreach ($optionTypes as $optionType) {
            DB::table('survey_accommodation_option_types')->updateOrInsert(
                ['key_name' => $optionType['key_name']],
                $optionType
            );
        }

        // Get option types and components for scoping
        $accOptionTypes = DB::table('survey_accommodation_option_types')->get()->keyBy('key_name');
        $accComponents = DB::table('survey_accommodation_components')->get()->keyBy('key_name');

        // Accommodation Options - Materials (scoped to components)
        if (isset($accOptionTypes['material'])) {
            $materialOptions = [
                // Ceiling materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Plasterboard', 'scope_type' => 'component', 'scope_id' => $accComponents['ceiling']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Lath and Plaster', 'scope_type' => 'component', 'scope_id' => $accComponents['ceiling']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Concrete', 'scope_type' => 'component', 'scope_id' => $accComponents['ceiling']->id ?? null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'LDFB', 'scope_type' => 'component', 'scope_id' => $accComponents['ceiling']->id ?? null, 'sort_order' => 4],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'AIB', 'scope_type' => 'component', 'scope_id' => $accComponents['ceiling']->id ?? null, 'sort_order' => 5],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Asbestos Ins Board', 'scope_type' => 'component', 'scope_id' => $accComponents['ceiling']->id ?? null, 'sort_order' => 6],
                
                // Walls materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Plasterboard', 'scope_type' => 'component', 'scope_id' => $accComponents['walls']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Plaster', 'scope_type' => 'component', 'scope_id' => $accComponents['walls']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Brick', 'scope_type' => 'component', 'scope_id' => $accComponents['walls']->id ?? null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Stone', 'scope_type' => 'component', 'scope_id' => $accComponents['walls']->id ?? null, 'sort_order' => 4],
                
                // Windows materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Double Glazed Aluminium', 'scope_type' => 'component', 'scope_id' => $accComponents['windows']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Single Glazed', 'scope_type' => 'component', 'scope_id' => $accComponents['windows']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'UPVC', 'scope_type' => 'component', 'scope_id' => $accComponents['windows']->id ?? null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Timber', 'scope_type' => 'component', 'scope_id' => $accComponents['windows']->id ?? null, 'sort_order' => 4],
                
                // Internal Door materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Timber', 'scope_type' => 'component', 'scope_id' => $accComponents['internal_door']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'MDF', 'scope_type' => 'component', 'scope_id' => $accComponents['internal_door']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Composite', 'scope_type' => 'component', 'scope_id' => $accComponents['internal_door']->id ?? null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Glass', 'scope_type' => 'component', 'scope_id' => $accComponents['internal_door']->id ?? null, 'sort_order' => 4],
                
                // External Door materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Timber', 'scope_type' => 'component', 'scope_id' => $accComponents['external_door']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'UPVC', 'scope_type' => 'component', 'scope_id' => $accComponents['external_door']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Aluminium', 'scope_type' => 'component', 'scope_id' => $accComponents['external_door']->id ?? null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Composite', 'scope_type' => 'component', 'scope_id' => $accComponents['external_door']->id ?? null, 'sort_order' => 4],
                
                // Floors materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Timber', 'scope_type' => 'component', 'scope_id' => $accComponents['floors']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Concrete', 'scope_type' => 'component', 'scope_id' => $accComponents['floors']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Tiles', 'scope_type' => 'component', 'scope_id' => $accComponents['floors']->id ?? null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Carpet', 'scope_type' => 'component', 'scope_id' => $accComponents['floors']->id ?? null, 'sort_order' => 4],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Vinyl', 'scope_type' => 'component', 'scope_id' => $accComponents['floors']->id ?? null, 'sort_order' => 5],
                
                // Services materials
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Standard', 'scope_type' => 'component', 'scope_id' => $accComponents['services']->id ?? null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Modern', 'scope_type' => 'component', 'scope_id' => $accComponents['services']->id ?? null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['material']->id, 'value' => 'Mixed', 'scope_type' => 'component', 'scope_id' => $accComponents['services']->id ?? null, 'sort_order' => 3],
            ];

            // Filter out null scope_ids
            $materialOptions = array_filter($materialOptions, function($opt) {
                return $opt['scope_id'] !== null;
            });

            foreach ($materialOptions as $option) {
                DB::table('survey_accommodation_options')->updateOrInsert(
                    [
                        'option_type_id' => $option['option_type_id'],
                        'value' => $option['value'],
                        'scope_type' => $option['scope_type'],
                        'scope_id' => $option['scope_id'],
                    ],
                    array_merge($option, ['is_active' => true])
                );
            }
        }

        // Accommodation Options - Defects (global)
        if (isset($accOptionTypes['defects'])) {
            $defectOptions = [
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'No Defects', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 1],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'Cracks', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 2],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'TEX', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 3],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'AIB', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 4],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'Bulging', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 5],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'Textured Coating', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 6],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'Staining', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 7],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'Stains', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 8],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'DRY STAIN', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 9],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'WET STAIN', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 10],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'POLYSTYRENE', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 11],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'CLADDING', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 12],
                ['option_type_id' => $accOptionTypes['defects']->id, 'value' => 'BER REPLACE', 'scope_type' => 'global', 'scope_id' => null, 'sort_order' => 13],
            ];

            foreach ($defectOptions as $option) {
                DB::table('survey_accommodation_options')->updateOrInsert(
                    [
                        'option_type_id' => $option['option_type_id'],
                        'value' => $option['value'],
                        'scope_type' => $option['scope_type'],
                        'scope_id' => $option['scope_id'],
                    ],
                    array_merge($option, ['is_active' => true])
                );
            }
        }

        $this->command->info('Accommodation master data seeded successfully!');
    }
}

