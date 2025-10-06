<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;

class VehicleReplacement extends Model
{
        protected $fillable = [
        'vehicle_id',
        'reason',
        'replacement_period',
        'notes',
        'status',
        'replacement_vehicle'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    public function replacement_vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'replacement_vehicle');
    }
    
    public function replacementVehicle()
    {
    return $this->belongsTo(Vehicle::class, 'replacement_vehicle');
    }
    
      public function replacement()
    {
        return $this->hasMany(VehicleReplacement::class, 'vehicle_id');
    }

}
