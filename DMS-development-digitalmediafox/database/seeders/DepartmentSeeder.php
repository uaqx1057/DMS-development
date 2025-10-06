<?php

namespace Database\Seeders;

use App\Traits\DepartmentTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    use DepartmentTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeDepartment();
    }
}
