<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Models\DriverType;

trait DriverTypeTrait
{
    public function storeDriverTypes()
    {
        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DriverType::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DriverType::insert($this->getDriverTypes());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    private function getDriverTypes()
    {
        return [
            [
                'name' => 'Sponsor',
                'fields' => 'vehicle_monthly_cost,mobile_data,accommodation,government_cost,fuel,gprs',
                'is_freelancer' => false
            ],
            [
                'name' => 'Freelancer (VMFG)',
                'fields' => 'vehicle_monthly_cost,mobile_data,fuel,gprs',
                'is_freelancer' => true
            ]
        ];
    }
}
