<?php

namespace App\Traits;

use App\Models\Business;
use Illuminate\Support\Facades\DB;

trait BusinessTrait
{
    public function storeBusiness()
    {
        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Business::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Business::insert($this->getBusinesses());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    private function  getBusinesses()
    {
        return [
            ['name' => 'Hamza'],
            ['name' => 'Speed Logistics'],
            ['name' => 'Speed Kitchen'],
            ['name' => 'Test Business 2'],
            ['name' => 'Hunger Station'],
            ['name' => 'Jahez'],
            ['name' => 'Ajex'],
            ['name' => 'iMile']
        ];
    }
}
