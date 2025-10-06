<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use App\Models\EmploymentType;

trait EmploymentTypeTrait
{
    public function storeEmploymentTypes()
    {
        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            EmploymentType::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            EmploymentType::insert($this->getEmploymentTypes());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    private function getEmploymentTypes()
    {
        return [
            ['name' => 'Full Time'],
            ['name' => 'Part Time'],
            ['name' => 'On Contract'],
            ['name' => 'Internship'],
            ['name' => 'Trainee'],

        ];
    }
}
