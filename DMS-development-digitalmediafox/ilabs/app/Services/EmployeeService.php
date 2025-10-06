<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\{DB, Log};

class EmployeeService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected EmployeeRepository $employeeRepository)
    {
        //
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try{
            $employee = $this->employeeRepository->create($data);
            Log::info("Employee Created", ['employee' => $employee]);
            DB::commit();
            return $employee;
        }catch(\Exception $e){
            Log::error("Employee Create Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try{
            $employee = $this->employeeRepository->update($data, $id);
            Log::info("Employee Updated", ['employee' => $employee]);
            DB::commit();
            return $employee;
        }catch(\Exception $e){
            Log::error("Employee Update Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function delete($id)
    {
        return $this->employeeRepository->delete($id);
    }

    public function all()
    {
        return $this->employeeRepository->all();
    }

    public function find($id)
    {
        return $this->employeeRepository->find($id);
    }

}
