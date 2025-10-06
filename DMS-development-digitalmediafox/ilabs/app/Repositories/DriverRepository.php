<?php

namespace App\Repositories;

use App\Interfaces\DriverInterface;
use App\Models\{Driver};

class DriverRepository implements DriverInterface
{
    public function all()
    {
        return Driver::all();
    }

    public function getCount($status, $filters)
    {
        $query = Driver::where('status', $status);

        if (!empty($filters['date_range'])) {
            $dates = explode(' to ', $filters['date_range']);
            $query->whereBetween('updated_at', [
                $dates[0],
                $dates[1] ?? $dates[0],
            ]);
        } else {
            $query->whereDate('updated_at', now()->format('Y-m-d'));
        }

        return $query->count();
    }


    public function create(array $data)
    {
        $data['driver_id'] = getLatestDriverId();
        $driver = Driver::create($data);
        $driver->businesses()->attach($data['business_ids']);
        return $driver;
    }

    public function update(array $data, $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->businesses()->sync($data['business_ids']);
        $driver->update($data);
        return $driver;
    }

    public function delete($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
    }

    public function find($id)
    {
        return Driver::with('driver_type')->findOrFail($id);
    }
}
