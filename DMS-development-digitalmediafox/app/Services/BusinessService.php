<?php

namespace App\Services;

use App\Repositories\BusinessRepository;
use Illuminate\Support\Facades\{DB, Log};

class BusinessService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected BusinessRepository $businessRepository)
    {
        //
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try{
            $business = $this->businessRepository->create($data);
            Log::info("Business Created", ['business' => $business]);
            DB::commit();
            return $business;
        }catch(\Exception $e){
            Log::error("Business Create Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try{
            $business = $this->businessRepository->update($data, $id);
            Log::info("Business Updated", ['business' => $business]);
            DB::commit();
            return $business;
        }catch(\Exception $e){
            Log::error("Business Update Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function delete($id)
    {
        return $this->businessRepository->delete($id);
    }

    public function all()
    {
        return $this->businessRepository->all();
    }

    public function find($id)
    {
        return $this->businessRepository->find($id);
    }

}
