<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Traits\RoleTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use RoleTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeRoles();
    }
}
