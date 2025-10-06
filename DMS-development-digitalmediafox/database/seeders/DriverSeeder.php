<?php

namespace Database\Seeders;

use App\Traits\DriverTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    use DriverTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeDrivers();
    }
}
