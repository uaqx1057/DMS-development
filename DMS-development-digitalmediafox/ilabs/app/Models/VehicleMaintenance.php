<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;

class VehicleMaintenance extends Model
{
     protected $fillable = [
        'vehicle_id', 'maintenance_type', 'description', 'urgency', 'status'
    ];

    public function vehicle()
   {
       return $this->belongsTo(Vehicle::class);
   }

}
