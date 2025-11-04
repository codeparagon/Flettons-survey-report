<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all administrative privileges',
            ],
            [
                'name' => 'surveyor',
                'display_name' => 'Surveyor',
                'description' => 'Survey management and data collection access',
            ],
            [
                'name' => 'client',
                'display_name' => 'Client',
                'description' => 'Limited access to view assigned data',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}








