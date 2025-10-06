<?php

namespace Database\Seeders;

use App\Traits\BranchTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    use BranchTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeBranches();
    }
}
