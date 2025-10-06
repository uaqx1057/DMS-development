<?php

namespace App\Traits;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;

trait BranchTrait
{

    public function storeBranches()
    {
        try {
            DB::beginTransaction();

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Branch::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Branch::insert($this->getBranches());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function getBranches()
    {
        return [
            ['name' => 'Jeddah'],
            ['name' => 'Dammam'],
            ['name' => 'Riyadh'],
            ['name' => 'Al Hasa'],
            ['name' => 'Jubail'],
        ];
    }


}
