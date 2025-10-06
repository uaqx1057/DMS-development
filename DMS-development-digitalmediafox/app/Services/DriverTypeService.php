<?php

namespace App\Services;

use App\Repositories\DriverTypeRepository;
use Illuminate\Support\Facades\{DB, Log};

class DriverTypeService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DriverTypeRepository $driverTypeRepository)
    {
        //
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try{
            $driver_type = $this->driverTypeRepository->create($data);
            Log::info("DriverType Created", ['driver_type' => $driver_type]);
            DB::commit();
            return $driver_type;
        }catch(\Exception $e){
            Log::error("DriverType Create Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try{
            $driver_type = $this->driverTypeRepository->update($data, $id);
            Log::info("DriverType Updated", ['driver_type' => $driver_type]);
            DB::commit();
            return $driver_type;
        }catch(\Exception $e){
            Log::error("DriverType Update Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function delete($id)
    {
        return $this->driverTypeRepository->delete($id);
    }

    public function all()
    {
        return $this->driverTypeRepository->all();
    }

    public function find($id)
    {
        return $this->driverTypeRepository->find($id);
    }

}
