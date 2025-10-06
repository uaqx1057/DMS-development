<?php

namespace Database\Seeders;

use App\Traits\CountryTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    use CountryTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeCountries();
    }
}
