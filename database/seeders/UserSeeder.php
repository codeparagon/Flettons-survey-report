<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $surveyorRole = Role::where('name', 'surveyor')->first();
        $clientRole = Role::where('name', 'client')->first();

        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'admin@flettons.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'status' => 'active',
            ]
        );

        // Create Surveyor
        User::updateOrCreate(
            ['email' => 'surveyor@flettons.com'],
            [
                'name' => 'John Surveyor',
                'password' => Hash::make('password'),
                'role_id' => $surveyorRole->id,
                'status' => 'active',
            ]
        );

        // Create Client
        User::updateOrCreate(
            ['email' => 'client@flettons.com'],
            [
                'name' => 'Jane Client',
                'password' => Hash::make('password'),
                'role_id' => $clientRole->id,
                'status' => 'active',
            ]
        );
    }
}











