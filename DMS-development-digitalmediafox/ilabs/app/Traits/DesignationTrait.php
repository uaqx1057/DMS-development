<?php

namespace App\Traits;

use App\Models\Designation;
use Illuminate\Support\Facades\DB;

trait DesignationTrait
{


    public function storeDesignations()
    {
        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Designation::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Designation::insert($this->getDesignations());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
    //
    private function getDesignations()
    {
        return [
            ['name' => 'IT Team Leader'],
            ['name' => 'Data Entry'],
            ['name' => 'Network Engineer'],
            ['name' => 'Chief Executive Officer CEO'],
            ['name' => 'IT Manager'],
            ['name' => 'Executive Personal Assistant'],
            ['name' => 'General Branch Manager'],
            ['name' => 'Branch Coordinator'],
            ['name' => 'Networking Engineer'],
            ['name' => 'Housing & Fleet Supervisor'],
            ['name' => 'Operation Supervisor'],
            ['name' => 'Fleet Coordinator'],
            ['name' => 'Project Manager'],
            ['name' => 'Website Developer'],
            ['name' => 'Web App Developer'],
            ['name' => 'Office Boy'],
            ['name' => 'Housing Supervisor'],
            ['name' => 'Accommodation Supervisor'],
            ['name' => 'Control Room Supervisor'],
            ['name' => 'Control Room Operator'],
            ['name' => 'Operation Manager'],
            ['name' => 'Data Entry Operator'],
            ['name' => 'Account Manager'],
            ['name' => 'Human Resources Supervisor'],
            ['name' => 'Human Resources Assistant'],
            ['name' => 'Graphic Designer'],
            ['name' => 'Public Relations'],
            ['name' => 'IT Technical Support & Help Desk'],
            ['name' => 'Senior Software Engineer'],
            ['name' => 'Cashier'],
            ['name' => 'Project Coordinator'],
            ['name' => 'Accountant'],
            ['name' => 'Sales and Marketing'],
            ['name' => 'Marketing Specialist'],
            ['name' => 'Branch Manager'],
            ['name' => 'Operation Coordinator'],
            ['name' => 'Branch Supervisor']

        ];
    }
}
