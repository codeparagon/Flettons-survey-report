<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get surveyor users
        $surveyorRole = Role::where('name', 'surveyor')->first();
        $surveyors = User::where('role_id', $surveyorRole->id)->get();
        
        if ($surveyors->isEmpty()) {
            $this->command->warn('No surveyors found. Please run UserSeeder first.');
            return;
        }

        // Sample survey data
        $surveys = [
            [
                // Basic client info
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email_address' => 'john.smith@example.com',
                'telephone_number' => '07700900123',
                'full_address' => '123 High Street, London',
                'postcode' => 'SW1A 1AA',
                'house_or_flat' => 'House',
                'number_of_bedrooms' => 3,
                'market_value' => 450000,
                'listed_building' => 'No',
                'over1650' => 'No',
                'sqft_area' => 1200,
                
                // Survey details
                'level' => 'Level 2',
                'level_total' => 650.00,
                'breakdown' => false,
                'aerial' => true,
                'insurance' => false,
                'addons' => false,
                
                // Infusionsoft fields
                'inf_field_FirstName' => 'John',
                'inf_field_LastName' => 'Smith',
                'inf_field_Email' => 'john.smith@example.com',
                'inf_field_Phone1' => '07700900123',
                'inf_field_StreetAddress1' => '123 High Street, London',
                'inf_field_PostalCode' => 'SW1A 1AA',
                'inf_field_Address2Street1' => '123 High Street, London',
                'inf_field_PostalCode2' => 'SW1A 1AA',
                
                // Property details
                'inf_custom_VacantorOccupied' => 'Occupied',
                'inf_custom_AnyExtensions' => 'Yes',
                'inf_custom_Garage' => 'Yes',
                'inf_custom_GarageLocation' => 'Detached',
                'inf_custom_Garden' => 'Yes',
                'inf_custom_GardenLocation' => 'Rear',
                'inf_custom_SpecificConcerns' => 'Minor cracks visible in exterior walls',
                
                // Management fields
                'surveyor_id' => $surveyors->first()->id,
                'status' => 'assigned',
                'payment_status' => 'paid',
                'scheduled_date' => now()->addDays(3),
                'current_step' => 0,
                'is_submitted' => 'true',
                'admin_notes' => 'Standard residential survey - priority booking',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email_address' => 'sarah.johnson@example.com',
                'telephone_number' => '07700900456',
                'full_address' => '45 Park Avenue, Manchester',
                'postcode' => 'M1 2AB',
                'house_or_flat' => 'Flat',
                'number_of_bedrooms' => 2,
                'market_value' => 280000,
                'listed_building' => 'No',
                'over1650' => 'No',
                'sqft_area' => 850,
                
                'level' => 'Level 3',
                'level_total' => 850.00,
                'breakdown' => true,
                'aerial' => true,
                'insurance' => true,
                'addons' => true,
                
                'inf_field_FirstName' => 'Sarah',
                'inf_field_LastName' => 'Johnson',
                'inf_field_Email' => 'sarah.johnson@example.com',
                'inf_field_Phone1' => '07700900456',
                'inf_field_StreetAddress1' => '45 Park Avenue, Manchester',
                'inf_field_PostalCode' => 'M1 2AB',
                'inf_field_Address2Street1' => '45 Park Avenue, Manchester',
                'inf_field_PostalCode2' => 'M1 2AB',
                
                'inf_custom_VacantorOccupied' => 'Vacant',
                'inf_custom_AnyExtensions' => 'No',
                'inf_custom_Garage' => 'No',
                'inf_custom_Garden' => 'No',
                'inf_custom_SpecificConcerns' => 'Concerns about roof condition and dampness',
                
                'surveyor_id' => $surveyors->first()->id,
                'status' => 'in_progress',
                'payment_status' => 'paid',
                'scheduled_date' => now()->addDays(1),
                'current_step' => 0,
                'is_submitted' => 'true',
                'admin_notes' => 'Client mentioned previous water damage - check thoroughly',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email_address' => 'michael.brown@example.com',
                'telephone_number' => '07700900789',
                'full_address' => '78 Oak Drive, Birmingham',
                'postcode' => 'B15 3TN',
                'house_or_flat' => 'House',
                'number_of_bedrooms' => 4,
                'market_value' => 595000,
                'listed_building' => 'Yes',
                'over1650' => 'Yes',
                'sqft_area' => 2100,
                
                'level' => 'Level 3',
                'level_total' => 1250.00,
                'breakdown' => true,
                'aerial' => true,
                'insurance' => true,
                'addons' => true,
                
                'inf_field_FirstName' => 'Michael',
                'inf_field_LastName' => 'Brown',
                'inf_field_Email' => 'michael.brown@example.com',
                'inf_field_Phone1' => '07700900789',
                'inf_field_StreetAddress1' => '78 Oak Drive, Birmingham',
                'inf_field_PostalCode' => 'B15 3TN',
                'inf_field_Address2Street1' => '78 Oak Drive, Birmingham',
                'inf_field_PostalCode2' => 'B15 3TN',
                
                'inf_custom_VacantorOccupied' => 'Occupied',
                'inf_custom_AnyExtensions' => 'Yes',
                'inf_custom_Garage' => 'Yes',
                'inf_custom_GarageLocation' => 'Attached',
                'inf_custom_Garden' => 'Yes',
                'inf_custom_GardenLocation' => 'Front and Rear',
                'inf_custom_SpecificConcerns' => 'Listed building - special attention needed for structural elements',
                
                'surveyor_id' => $surveyors->count() > 1 ? $surveyors->skip(1)->first()->id : $surveyors->first()->id,
                'status' => 'pending',
                'payment_status' => 'paid',
                'scheduled_date' => now()->addDays(7),
                'current_step' => 0,
                'is_submitted' => 'true',
                'admin_notes' => 'Listed building - Grade II. Requires specialist knowledge.',
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Williams',
                'email_address' => 'emma.williams@example.com',
                'telephone_number' => '07700901234',
                'full_address' => '12 Riverside Court, Leeds',
                'postcode' => 'LS1 4BZ',
                'house_or_flat' => 'Flat',
                'number_of_bedrooms' => 1,
                'market_value' => 185000,
                'listed_building' => 'No',
                'over1650' => 'No',
                'sqft_area' => 600,
                
                'level' => 'Level 1',
                'level_total' => 450.00,
                'breakdown' => false,
                'aerial' => false,
                'insurance' => false,
                'addons' => false,
                
                'inf_field_FirstName' => 'Emma',
                'inf_field_LastName' => 'Williams',
                'inf_field_Email' => 'emma.williams@example.com',
                'inf_field_Phone1' => '07700901234',
                'inf_field_StreetAddress1' => '12 Riverside Court, Leeds',
                'inf_field_PostalCode' => 'LS1 4BZ',
                'inf_field_Address2Street1' => '12 Riverside Court, Leeds',
                'inf_field_PostalCode2' => 'LS1 4BZ',
                
                'inf_custom_VacantorOccupied' => 'Occupied',
                'inf_custom_AnyExtensions' => 'No',
                'inf_custom_Garage' => 'No',
                'inf_custom_Garden' => 'No',
                'inf_custom_SpecificConcerns' => 'None - first time buyer, standard assessment needed',
                
                'surveyor_id' => $surveyors->first()->id,
                'status' => 'completed',
                'payment_status' => 'paid',
                'scheduled_date' => now()->subDays(2),
                'current_step' => 0,
                'is_submitted' => 'true',
                'admin_notes' => 'Completed survey - report delivered',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Taylor',
                'email_address' => 'david.taylor@example.com',
                'telephone_number' => '07700905678',
                'full_address' => '89 Victoria Road, Bristol',
                'postcode' => 'BS1 6HY',
                'house_or_flat' => 'House',
                'number_of_bedrooms' => 5,
                'market_value' => 725000,
                'listed_building' => 'No',
                'over1650' => 'Yes',
                'sqft_area' => 2800,
                
                'level' => 'Level 4',
                'level_total' => 1450.00,
                'breakdown' => true,
                'aerial' => true,
                'insurance' => true,
                'addons' => true,
                
                'inf_field_FirstName' => 'David',
                'inf_field_LastName' => 'Taylor',
                'inf_field_Email' => 'david.taylor@example.com',
                'inf_field_Phone1' => '07700905678',
                'inf_field_StreetAddress1' => '89 Victoria Road, Bristol',
                'inf_field_PostalCode' => 'BS1 6HY',
                'inf_field_Address2Street1' => '89 Victoria Road, Bristol',
                'inf_field_PostalCode2' => 'BS1 6HY',
                
                'inf_custom_VacantorOccupied' => 'Vacant',
                'inf_custom_AnyExtensions' => 'Yes',
                'inf_custom_Garage' => 'Yes',
                'inf_custom_GarageLocation' => 'Detached',
                'inf_custom_Garden' => 'Yes',
                'inf_custom_GardenLocation' => 'Rear',
                'inf_custom_SpecificConcerns' => 'Property built pre-1700. Extensive historical modifications.',
                
                'surveyor_id' => null, // Not yet assigned
                'status' => 'pending',
                'payment_status' => 'pending',
                'scheduled_date' => null,
                'current_step' => 0,
                'is_submitted' => 'true',
                'admin_notes' => 'Awaiting surveyor assignment - complex property',
            ],
        ];

        foreach ($surveys as $surveyData) {
            Survey::create($surveyData);
        }

        $this->command->info('Survey seeder completed successfully!');
        $this->command->info('Created ' . count($surveys) . ' sample surveys.');
    }
}



