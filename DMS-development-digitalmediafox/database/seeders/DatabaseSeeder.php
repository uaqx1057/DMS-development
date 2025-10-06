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
            BranchSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            FieldSeeder::class,
            DriverTypeSeeder::class,
            ModuleSeeder::class,
            RoleSeeder::class,
            CountrySeeder::class,
            LanguageSeeder::class,
            EmploymentTypeSeeder::class,
            UserSeeder::class,
            DriverSeeder::class,
        ]);

    }
}
