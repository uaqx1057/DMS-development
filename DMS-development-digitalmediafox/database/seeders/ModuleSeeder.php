<?php

namespace Database\Seeders;

use App\Traits\ModuleTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    use ModuleTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeModules();
    }
}
