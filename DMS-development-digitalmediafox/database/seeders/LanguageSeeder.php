<?php

namespace Database\Seeders;

use App\Traits\LanguageTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    use LanguageTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->storeLanguages();
    }
}
