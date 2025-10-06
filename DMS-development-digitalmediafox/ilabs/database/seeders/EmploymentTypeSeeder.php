<?php

namespace Database\Seeders;

use App\Traits\EmploymentTypeTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentTypeSeeder extends Seeder
{
    use EmploymentTypeTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeEmploymentTypes();
    }
}
