<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Traits\BusinessTrait;
class BusinessSeeder extends Seeder
{
    use BusinessTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeBusiness();
    }
}
