<?php

namespace App\Services;

use App\Repositories\FieldRepository;
use Illuminate\Support\Facades\{DB, Log};

class FieldService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected FieldRepository $fieldRepository)
    {
        //
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try{
            $field = $this->fieldRepository->create($data);
            Log::info("Field Created", ['field' => $field]);
            DB::commit();
            return $field;
        }catch(\Exception $e){
            Log::error("Field Create Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try{
            $field = $this->fieldRepository->update($data, $id);
            Log::info("Field Updated", ['field' => $field]);
            DB::commit();
            return $field;
        }catch(\Exception $e){
            Log::error("Field Update Error", ['error' => $e]);
            DB::rollBack();
        }
    }

    public function delete($id)
    {
        return $this->fieldRepository->delete($id);
    }

    public function all()
    {
        return $this->fieldRepository->all();
    }

    public function find($id)
    {
        return $this->fieldRepository->find($id);
    }

}
