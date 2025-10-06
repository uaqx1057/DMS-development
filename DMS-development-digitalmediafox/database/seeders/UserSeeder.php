<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\EmployeeTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use EmployeeTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->storeUsers();
    }
}
