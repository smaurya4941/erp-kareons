<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Creates the Admin/MR roles and the default admin account.
        $this->call([
            RoleAndAdminSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
