<?php

namespace App\Repositories;

use App\Interfaces\BusinessInterface;
use App\Models\Business;

class BusinessRepository implements BusinessInterface
{
    public function all()
    {
        return Business::all();
    }

    public function create(array $data)
    {
        $business = Business::create($data);
        if(isset($data['fields'])){
            $business->fields()->attach($data['fields']);
        }
        if(isset($data['driver_calculations'])){
            $business->driver_calculations()->createMany($data['driver_calculations']);
        }
        return $business;
    }

    public function update(array $data, $id)
    {
        $business = Business::findOrFail($id);
        $business->fields()->sync($data['fields']);

        $business->update($data);
        return $business;
    }

    public function delete($id)
    {
        $business = Business::findOrFail($id);
        $business->delete();
    }

    public function find($id)
    {
        return Business::with('fields')->findOrFail($id);
    }
}
