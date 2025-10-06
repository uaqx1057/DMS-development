<?php

namespace Database\Seeders;

use App\Traits\DriverTypeTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverTypeSeeder extends Seeder
{
    use DriverTypeTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeDriverTypes();
    }
}
