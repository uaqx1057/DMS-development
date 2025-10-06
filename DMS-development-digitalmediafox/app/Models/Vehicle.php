<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'registration_number',
        'make',
        'model',
        'year',
        'vin',
        'current_location',
        'status',
        'fuel_type',
        'mileage',
        'notes',
        'branch_id',
    ];
    
    public function assignedDriver()
    {
        return $this->hasOne(\App\Models\AssignDriver::class, 'vehicle_id')
                    ->where('status', 'active')
                    ->latest();
    }
    
    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }


}
