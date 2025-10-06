<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignDriver extends Model
{
     use HasFactory;
     protected $fillable = [
        'vehicle_id',
        'driver_id',
        'assign_date',
        'status',
    ];

        // Only return active vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->where('status', 'active');
    }
    
    // Only return active driver
    public function driver()
    {
        return $this->belongsTo(Driver::class)->where('status', 'active');
    }


}
