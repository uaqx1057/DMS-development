<?php

namespace App\Repositories;

use App\Interfaces\DriverTypeInterface;
use App\Models\DriverType;
use App\Models\User;

class DriverTypeRepository implements DriverTypeInterface
{
    public function all()
    {
        return DriverType::all();
    }

    public function create(array $data)
    {
        return DriverType::create($data);
    }

    public function update(array $data, $id)
    {
        $driver_type = DriverType::findOrFail($id);
        $driver_type->update($data);
        return $driver_type;
    }

    public function delete($id)
    {
        $driver_type = DriverType::findOrFail($id);
        $driver_type->delete();
    }

    public function find($id)
    {
        return DriverType::findOrFail($id);
    }
}
