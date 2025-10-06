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
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
