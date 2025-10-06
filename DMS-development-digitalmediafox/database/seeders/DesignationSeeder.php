<?php

namespace Database\Seeders;

use App\Traits\DesignationTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    use DesignationTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeDesignations();
    }
}
