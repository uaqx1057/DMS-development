<?php

namespace Database\Seeders;

use App\Traits\FieldTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    use FieldTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeFields();
    }
}
