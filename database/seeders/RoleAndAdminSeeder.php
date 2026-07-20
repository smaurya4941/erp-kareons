<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $mrRole = Role::firstOrCreate(['name' => 'MR']);

        // Create or Update Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@kareons.com'],
            [
                'employee_code' => 'ADM0001',
                'name' => 'System Administrator',
                'password' => Hash::make('Kareons@2026'),
                'mobile' => '9999999999',
                'status' => true,
            ]
        );

        // Assign Role
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
        }
    }
}
