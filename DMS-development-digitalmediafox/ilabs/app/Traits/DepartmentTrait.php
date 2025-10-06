<?php

namespace App\Traits;

use App\Models\Department;
use Illuminate\Support\Facades\DB;

trait DepartmentTrait
{
    //


    public function storeDepartment()
    {
        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Department::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Department::insert($this->getDepartments());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
    private function getDepartments()
    {

        return [
            ['name' => 'Information Technology'],
            ['name' => 'Administration'],
            ['name' => 'Human Resources'],
            ['name' => 'Finance'],
            ['name' => 'Control Room'],
            ['name' => 'Operations'],
            ['name' => 'Chief Executive Officer CEO'],
            ['name' => 'IT Manager'],
            ['name' => 'Branch Manager'],
            ['name' => 'Sales And Marketing'],
            ['name' => 'Marketing'],
            ['name' => 'Management']
        ];
    }
}
