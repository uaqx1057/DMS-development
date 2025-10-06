<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelExpense extends Model
{
    protected $fillable = [
        'vehicle_id',
        'fuel_type',
        'fuel_station',
        'liters',
        'amount_paid',
        'odometer_reading',
        'distance_since_last_refuel',
        'receipt_image',
        'notes',
        'recorded_by',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

}

