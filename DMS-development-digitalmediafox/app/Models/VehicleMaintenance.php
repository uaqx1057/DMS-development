<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;

class VehicleMaintenance extends Model
{
     protected $fillable = [
        'vehicle_id', 'maintenance_type', 'description', 'urgency', 'status', 'request_by'
    ];

    public function vehicle()
   {
       return $this->belongsTo(Vehicle::class);
   }
   
 
    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class, 'vehicle_id');
    }


}
